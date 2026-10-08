<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\{Auth,Csrf,Database,View};

$pdo = Database::connection();
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($path === 'language' && $method === 'POST') {
    Csrf::verify(); $_SESSION['lang'] = ($_POST['lang'] ?? 'id') === 'en' ? 'en' : 'id';
    $back = parse_url((string)($_SERVER['HTTP_REFERER'] ?? '/'), PHP_URL_PATH) ?: '/'; header('Location: ' . $back); exit;
}

if ($path === 'login') {
    if ($method === 'POST') {
        Csrf::verify();
        if (Auth::attempt((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''))) redirect('');
        flash('error', 'Email atau kata sandi tidak sesuai.');
    }
    View::render('auth/login', ['title' => 'Masuk']); exit;
}
if ($path === 'logout' && $method === 'POST') { Csrf::verify(); Auth::logout(); redirect('login'); }
if ($path === '' && !Auth::user()) {
    View::render('landing/index', ['title' => __('SIMIT — Sistem Inventaris dan Manajemen IT','SIMIT — IT Inventory and Management System')]); exit;
}
Auth::requireLogin();

if ($path === 'notifikasi') {
    $uid=(int)Auth::user()['id'];
    if($method==='POST'){Csrf::verify();$pdo->prepare('UPDATE notifications SET read_at=CURRENT_TIMESTAMP WHERE user_id=? AND read_at IS NULL')->execute([$uid]);flash('success',__('Semua notifikasi ditandai sudah dibaca.','All notifications marked as read.'));redirect('notifikasi');}
    $stmt=$pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC,id DESC LIMIT 100');$stmt->execute([$uid]);View::render('notifications/index',['title'=>__('Notifikasi','Notifications'),'rows'=>$stmt->fetchAll()]);exit;
}

if (preg_match('#^notifikasi/buka/(\d+)$#',$path,$m)) {
    $stmt=$pdo->prepare('SELECT link_url FROM notifications WHERE id=? AND user_id=?');$stmt->execute([(int)$m[1],Auth::user()['id']]);$notification=$stmt->fetch();
    if(!$notification){http_response_code(404);exit('Notifikasi tidak ditemukan.');}$pdo->prepare('UPDATE notifications SET read_at=COALESCE(read_at,CURRENT_TIMESTAMP) WHERE id=? AND user_id=?')->execute([(int)$m[1],Auth::user()['id']]);$target=(string)($notification['link_url']?:'/notifikasi');if(!str_starts_with($target,'/')||str_starts_with($target,'//'))$target='/notifikasi';header('Location: '.$target);exit;
}

if ($path === 'chat' || $path === 'chat/send') {
    $uid=(int)Auth::user()['id'];$peerId=(int)($_GET['user']??$_POST['recipient_id']??0);
    if($path==='chat/send'&&$method==='POST'){Csrf::verify();$body=trim((string)($_POST['body']??''));if($peerId===$uid||$body===''||mb_strlen($body)>2000){flash('error',__('Pesan tidak valid.','Invalid message.'));redirect('chat'.($peerId?'?user='.$peerId:''));}$check=$pdo->prepare('SELECT id FROM users WHERE id=? AND is_active=1');$check->execute([$peerId]);if(!$check->fetchColumn()){flash('error',__('Penerima tidak ditemukan.','Recipient not found.'));redirect('chat');}$pdo->prepare('INSERT INTO messages(sender_id,recipient_id,body) VALUES(?,?,?)')->execute([$uid,$peerId,$body]);notify_user($peerId,__('Pesan chat baru','New chat message'),Auth::user()['name'].': '.mb_strimwidth($body,0,90,'…'),'/chat?user='.$uid);redirect('chat?user='.$peerId);}
    $users=$pdo->prepare("SELECT u.id,u.name,u.role,(SELECT COUNT(*) FROM messages m WHERE m.sender_id=u.id AND m.recipient_id=? AND m.read_at IS NULL) unread,(SELECT MAX(created_at) FROM messages m2 WHERE (m2.sender_id=u.id AND m2.recipient_id=?) OR (m2.sender_id=? AND m2.recipient_id=u.id)) last_message FROM users u WHERE u.is_active=1 AND u.id<>? ORDER BY CASE WHEN last_message IS NULL THEN 1 ELSE 0 END,last_message DESC,u.name");$users->execute([$uid,$uid,$uid,$uid]);$contacts=$users->fetchAll();$messages=[];$peer=null;if($peerId){$s=$pdo->prepare('SELECT id,name,role FROM users WHERE id=? AND is_active=1');$s->execute([$peerId]);$peer=$s->fetch();if($peer){$pdo->prepare('UPDATE messages SET read_at=CURRENT_TIMESTAMP WHERE sender_id=? AND recipient_id=? AND read_at IS NULL')->execute([$peerId,$uid]);$s=$pdo->prepare('SELECT m.*,s.name sender_name FROM messages m JOIN users s ON s.id=m.sender_id WHERE (m.sender_id=? AND m.recipient_id=?) OR (m.sender_id=? AND m.recipient_id=?) ORDER BY m.created_at ASC,m.id ASC LIMIT 300');$s->execute([$uid,$peerId,$peerId,$uid]);$messages=$s->fetchAll();}}
    View::render('chat/index',['title'=>'Chat','contacts'=>$contacts,'messages'=>$messages,'peer'=>$peer]);exit;
}

if ($path === 'profil') {
    $user=Auth::user();
    if($method==='POST'){
        Csrf::verify();$name=trim((string)($_POST['name']??''));$email=strtolower(trim((string)($_POST['email']??'')));$phone=trim((string)($_POST['phone']??''));$bio=trim((string)($_POST['bio']??''));$employeeId=trim((string)($_POST['employee_id']??''));$position=trim((string)($_POST['position']??''));$department=trim((string)($_POST['department']??''));$address=trim((string)($_POST['address']??''));$birthDate=trim((string)($_POST['birth_date']??''));$password=(string)($_POST['password']??'');
        if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||($password!==''&&strlen($password)<8)||($birthDate!==''&&!preg_match('/^\d{4}-\d{2}-\d{2}$/',$birthDate))){flash('error',__('Data profil belum valid. Kata sandi minimal 8 karakter.','Profile data is invalid. Password must be at least 8 characters.'));redirect('profil');}
        $newAvatar=null;$newAvatarMime=null;$oldAvatar=null;
        try{
            if(isset($_FILES['avatar'])&&$_FILES['avatar']['error']!==UPLOAD_ERR_NO_FILE){$f=$_FILES['avatar'];if($f['error']!==UPLOAD_ERR_OK||$f['size']>2097152)throw new RuntimeException(__('Foto profil maksimal 2 MB.','Profile photo may not exceed 2 MB.'));$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];if(!isset($allowed[$mime])||!getimagesize($f['tmp_name']))throw new RuntimeException(__('Foto harus berformat JPG, PNG, atau WebP.','Photo must be JPG, PNG, or WebP.'));$avatarDir=BASE_PATH.'/storage/avatars';if(!is_dir($avatarDir)&&!mkdir($avatarDir,0775,true)&&!is_dir($avatarDir))throw new RuntimeException(__('Folder foto tidak dapat dibuat.','Photo folder could not be created.'));$newAvatar=bin2hex(random_bytes(20)).'.'.$allowed[$mime];if(!move_uploaded_file($f['tmp_name'],$avatarDir.'/'.$newAvatar))throw new RuntimeException(__('Foto gagal disimpan.','Photo could not be saved.'));$newAvatarMime=$mime;$old=$pdo->prepare('SELECT avatar_stored_name FROM users WHERE id=?');$old->execute([$user['id']]);$oldAvatar=$old->fetchColumn()?:null;}
            $sql='UPDATE users SET name=?,email=?,phone=?,bio=?,employee_id=?,position=?,department=?,address=?,birth_date=?,updated_at=CURRENT_TIMESTAMP';$args=[$name,$email,$phone?:null,$bio?:null,$employeeId?:null,$position?:null,$department?:null,$address?:null,$birthDate?:null];if($newAvatar){$sql.=',avatar_stored_name=?,avatar_mime_type=?';$args[]=$newAvatar;$args[]=$newAvatarMime;}if($password!==''){$sql.=',password_hash=?';$args[]=password_hash($password,PASSWORD_DEFAULT);}$sql.=' WHERE id=?';$args[]=$user['id'];$pdo->prepare($sql)->execute($args);if($oldAvatar&&$newAvatar&&is_file(BASE_PATH.'/storage/avatars/'.$oldAvatar))unlink(BASE_PATH.'/storage/avatars/'.$oldAvatar);Auth::audit('update','users',(int)$user['id'],'Profile updated');flash('success',__('Profil berhasil diperbarui.','Profile updated successfully.'));
        }catch(Throwable $e){if($newAvatar&&is_file(BASE_PATH.'/storage/avatars/'.$newAvatar))unlink(BASE_PATH.'/storage/avatars/'.$newAvatar);flash('error',$e instanceof PDOException?__('Email atau ID pegawai sudah digunakan.','Email or employee ID is already in use.'):$e->getMessage());}redirect('profil');
    }
    $stmt=$pdo->prepare('SELECT id,name,email,role,phone,bio,employee_id,position,department,address,birth_date,avatar_stored_name FROM users WHERE id=?');$stmt->execute([$user['id']]);View::render('profile/index',['title'=>__('Profil Saya','My Profile'),'profile'=>$stmt->fetch()]);exit;
}

if (preg_match('#^foto-profil/(\d+)$#',$path,$m)) {
    $stmt=$pdo->prepare('SELECT avatar_stored_name,avatar_mime_type FROM users WHERE id=? AND is_active=1');$stmt->execute([(int)$m[1]]);$avatar=$stmt->fetch();$full=$avatar&&$avatar['avatar_stored_name']?BASE_PATH.'/storage/avatars/'.$avatar['avatar_stored_name']:'';
    if(!$full||!is_file($full)){http_response_code(404);exit('Foto tidak ditemukan.');}header('Content-Type: '.$avatar['avatar_mime_type']);header('Content-Length: '.filesize($full));header('Cache-Control: private, max-age=3600');readfile($full);exit;
}

if (preg_match('#^lampiran/(\d+)$#',$path,$m)) {
    $stmt=$pdo->prepare('SELECT * FROM attachments WHERE id=?');$stmt->execute([(int)$m[1]]);$file=$stmt->fetch();
    if(!$file){http_response_code(404);exit('File tidak ditemukan.');}$full=BASE_PATH.'/storage/uploads/'.$file['stored_name'];if(!is_file($full)){http_response_code(404);exit('File tidak tersedia.');}
    header('Content-Type: '.$file['mime_type']);header('Content-Length: '.filesize($full));header('Content-Disposition: attachment; filename="'.rawurlencode($file['original_name']).'"');readfile($full);exit;
}

if (preg_match('#^tugas/hasil/(\d+)$#',$path,$m)) {
    $taskId=(int)$m[1];$stmt=$pdo->prepare('SELECT t.*,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assignee_id WHERE t.id=?');$stmt->execute([$taskId]);$task=$stmt->fetch();if(!$task){http_response_code(404);exit('Tugas tidak ditemukan.');}if(!Auth::isAdmin()&&$task['assignee_id']!==null&&(int)$task['assignee_id']!==(int)Auth::user()['id']){http_response_code(403);View::render('crud/error',['title'=>__('Akses ditolak','Access denied'),'message'=>__('Tugas ini sedang dikerjakan pengguna lain.','This task is being handled by another user.')]);exit;}
    if($method==='POST'){
        if(!in_array($task['status'],['Tersedia','Dikerjakan','Tertunda'],true)){flash('error',__('Tugas sedang menunggu ACC atau sudah selesai.','The task is awaiting approval or already completed.'));redirect('tugas/hasil/'.$taskId);}
        Csrf::verify();$result=trim((string)($_POST['result']??''));if($result===''){flash('error',__('Teks hasil wajib diisi.','Result text is required.'));redirect('tugas/hasil/'.$taskId);}
        $pdo->beginTransaction();try{$claim=$pdo->prepare("UPDATE tasks SET assignee_id=COALESCE(assignee_id,?),result=?,status='Menunggu Verifikasi',updated_at=CURRENT_TIMESTAMP WHERE id=? AND status IN ('Tersedia','Dikerjakan','Tertunda') AND (assignee_id IS NULL OR assignee_id=?)");$claim->execute([Auth::user()['id'],$result,$taskId,Auth::user()['id']]);if(!$claim->rowCount())throw new RuntimeException('Tugas sudah dikerjakan pengguna lain.');
            if(isset($_FILES['attachment'])&&$_FILES['attachment']['error']!==UPLOAD_ERR_NO_FILE){$f=$_FILES['attachment'];if($f['error']!==UPLOAD_ERR_OK||$f['size']>10485760)throw new RuntimeException('Ukuran file maksimal 10 MB.');$mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);$allowed=['image/jpeg'=>'jpg','image/png'=>'png','application/pdf'=>'pdf'];if(!isset($allowed[$mime]))throw new RuntimeException('Format file harus JPG, PNG, atau PDF.');$stored=bin2hex(random_bytes(20)).'.'.$allowed[$mime];if(!move_uploaded_file($f['tmp_name'],BASE_PATH.'/storage/uploads/'.$stored))throw new RuntimeException('Gagal menyimpan file.');$pdo->prepare('INSERT INTO attachments(task_id,uploader_id,original_name,stored_name,mime_type,size_bytes) VALUES(?,?,?,?,?,?)')->execute([$taskId,Auth::user()['id'],basename($f['name']),$stored,$mime,$f['size']]);}
            $admins=$pdo->query("SELECT id FROM users WHERE role='admin' AND is_active=1")->fetchAll(PDO::FETCH_COLUMN);foreach($admins as $adminId)notify_user((int)$adminId,'Tugas menunggu ACC',Auth::user()['name'].' mengumpulkan: '.$task['title'],'/persetujuan');Auth::audit('result','tasks',$taskId,'Task result submitted');$pdo->commit();flash('success',__('Hasil pekerjaan berhasil disimpan.','Work result saved successfully.'));}catch(Throwable $e){$pdo->rollBack();flash('error',$e->getMessage());}redirect('tugas/hasil/'.$taskId);
    }
    $stmt=$pdo->prepare('SELECT a.*,u.name uploader FROM attachments a LEFT JOIN users u ON u.id=a.uploader_id WHERE task_id=? ORDER BY a.id DESC');$stmt->execute([$taskId]);View::render('tasks/result_v2',['title'=>__('Hasil Pekerjaan','Work Result'),'task'=>$task,'attachments'=>$stmt->fetchAll()]);exit;
}

if ($path === '') {
    $stats = [
        'pc' => (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE type='PC' AND archived_at IS NULL")->fetchColumn(),
        'problem' => (int)$pdo->query("SELECT COUNT(*) FROM assets WHERE status IN ('Perlu Servis','Rusak') AND archived_at IS NULL")->fetchColumn(),
        'units' => (int)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM assets WHERE type='Perangkat' AND archived_at IS NULL")->fetchColumn(),
        'open_tasks' => (int)$pdo->query("SELECT COUNT(*) FROM tasks WHERE status NOT IN ('Selesai','Dibatalkan','Menunggu Verifikasi')")->fetchColumn(),
    ];
    $attention=$pdo->query("SELECT a.*,l.name location FROM assets a LEFT JOIN locations l ON l.id=a.location_id WHERE a.status IN ('Perlu Servis','Rusak') AND a.archived_at IS NULL ORDER BY a.updated_at DESC LIMIT 5")->fetchAll();
    if(Auth::isAdmin()){$tasks=$pdo->query("SELECT t.*,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assignee_id WHERE t.status NOT IN ('Selesai','Dibatalkan','Menunggu Verifikasi') ORDER BY CASE t.priority WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 ELSE 3 END, t.created_at DESC LIMIT 6")->fetchAll();}else{$stmt=$pdo->prepare("SELECT t.*,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assignee_id WHERE t.status NOT IN ('Selesai','Dibatalkan','Menunggu Verifikasi') AND (t.status='Tersedia' OR t.assignee_id=?) ORDER BY CASE t.priority WHEN 'Tinggi' THEN 1 WHEN 'Sedang' THEN 2 ELSE 3 END,t.created_at DESC LIMIT 6");$stmt->execute([Auth::user()['id']]);$tasks=$stmt->fetchAll();}
    View::render('dashboard/index',compact('stats','attention','tasks')+['title'=>'Dashboard']); exit;
}

$configs = [
 'pc'=>['table'=>'assets','title'=>'Inventaris PC','type'=>'PC','fields'=>['code'=>'Kode aset','name'=>'Nama tampilan','pc_name'=>'Nama komputer','location_id'=>'Lokasi','ip_address'=>'Alamat IP','processor'=>'Prosesor','ram_gb'=>'RAM (GB)','storage_type'=>'Tipe storage','storage_value'=>'Kapasitas/ruang','os'=>'Sistem operasi','os_version'=>'Versi OS','status'=>'Status','notes'=>'Catatan'],'selects'=>['location_id'=>['table'=>'locations'],'storage_type'=>['HDD','SSD','SSD+HDD'],'status'=>['Baik','Perlu Servis','Rusak','Tidak Aktif']]],
 'perangkat'=>['table'=>'assets','title'=>'Perangkat Lain','type'=>'Perangkat','fields'=>['code'=>'Kode inventaris','name'=>'Nama perangkat','category_id'=>'Kategori','brand'=>'Merek','model'=>'Model','quantity'=>'Jumlah unit','location_id'=>'Lokasi','status'=>'Status','physical_condition'=>'Kondisi fisik','notes'=>'Catatan'],'selects'=>['category_id'=>['table'=>'asset_categories'],'location_id'=>['table'=>'locations'],'status'=>['Baik','Perlu Servis','Rusak','Tidak Aktif'],'physical_condition'=>['Baik','Perlu Servis','Rusak']]],
 'software'=>['table'=>'software','title'=>'Software','fields'=>['name'=>'Nama aplikasi','category'=>'Kategori','version'=>'Versi','size'=>'Ukuran','status'=>'Status','purpose'=>'Keperluan','starts_at'=>'Mulai penggunaan','ends_at'=>'Selesai penggunaan','notes'=>'Catatan'],'selects'=>['status'=>['Terinstall','Perlu Install','Perlu Dihapus','Sudah Dihapus']]],
 'tugas'=>['table'=>'tasks','title'=>'Tugas IT','fields'=>['number'=>'Nomor','title'=>'Judul tugas','description'=>'Deskripsi','instructions'=>'Instruksi','category'=>'Kategori','priority'=>'Prioritas','estimate_minutes'=>'Estimasi (menit)','assignee_id'=>'PIC','status'=>'Status','due_at'=>'Tenggat','result'=>'Hasil'],'selects'=>['priority'=>['Rendah','Sedang','Tinggi'],'assignee_id'=>['table'=>'users'],'status'=>['Draft','Tersedia','Dikerjakan','Tertunda','Menunggu Verifikasi','Selesai','Dibatalkan']]],
];

$parts=explode('/',$path); $module=$parts[0]; $action=$parts[1]??'index'; $id=isset($parts[2])?(int)$parts[2]:0;

if ($module === 'laporan') {
    $rows=$pdo->query("SELECT l.name location,COUNT(a.id) asset_groups,COALESCE(SUM(a.quantity),0) units,SUM(CASE WHEN a.status='Baik' THEN 1 ELSE 0 END) healthy,SUM(CASE WHEN a.status IN ('Perlu Servis','Rusak') THEN 1 ELSE 0 END) problem FROM locations l LEFT JOIN assets a ON a.location_id=l.id AND a.archived_at IS NULL GROUP BY l.id ORDER BY l.name")->fetchAll();
    $taskStats=$pdo->query("SELECT status,COUNT(*) total FROM tasks GROUP BY status ORDER BY status")->fetchAll();
    $softwareStats=$pdo->query("SELECT status,COUNT(*) total FROM software GROUP BY status ORDER BY status")->fetchAll();
    $userStats=$pdo->query("SELECT role,COUNT(*) total,SUM(is_active) active FROM users GROUP BY role ORDER BY role")->fetchAll();
    $recent=$pdo->query("SELECT t.number,t.title,t.status,t.completed_at,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assignee_id WHERE t.status='Selesai' ORDER BY t.completed_at DESC,t.id DESC LIMIT 10")->fetchAll();
    View::render('crud/report_v2',['title'=>'Laporan SIMIT','rows'=>$rows,'taskStats'=>$taskStats,'softwareStats'=>$softwareStats,'userStats'=>$userStats,'recent'=>$recent]); exit;
}
if ($module === 'persetujuan') {
    Auth::requireAdmin();
    if($action==='index'){$rows=$pdo->query("SELECT t.*,u.name assignee FROM tasks t LEFT JOIN users u ON u.id=t.assignee_id WHERE t.status='Menunggu Verifikasi' ORDER BY t.updated_at ASC")->fetchAll();View::render('tasks/approval',['title'=>'ACC Tugas','rows'=>$rows]);exit;}
    if(in_array($action,['approve','reject'],true)&&$method==='POST'){Csrf::verify();$note=trim((string)($_POST['note']??''));$to=$action==='approve'?'Selesai':'Dikerjakan';$taskInfo=$pdo->prepare('SELECT title,assignee_id FROM tasks WHERE id=?');$taskInfo->execute([$id]);$info=$taskInfo->fetch();$stmt=$pdo->prepare("UPDATE tasks SET status=?,verified_by=?,completed_at=CASE WHEN ?='Selesai' THEN CURRENT_TIMESTAMP ELSE NULL END,revision_note=?,updated_at=CURRENT_TIMESTAMP WHERE id=? AND status='Menunggu Verifikasi'");$stmt->execute([$to,Auth::user()['id'],$to,$note?:null,$id]);if($stmt->rowCount()){$pdo->prepare('INSERT INTO task_status_history(task_id,from_status,to_status,changed_by,note) VALUES(?,?,?,?,?)')->execute([$id,'Menunggu Verifikasi',$to,Auth::user()['id'],$note?:null]);if($info&&$info['assignee_id'])notify_user((int)$info['assignee_id'],$action==='approve'?'Tugas disetujui':'Tugas perlu revisi',($info['title']??'Tugas').($note?' — '.$note:''),'/tugas/hasil/'.$id);Auth::audit($action,'tasks',$id,'Task approval');flash('success',$action==='approve'?'Tugas disetujui dan masuk riwayat.':'Tugas dikembalikan kepada user.');}redirect('persetujuan');}
}
if ($module === 'audit') {
    Auth::requireAdmin(); $rows=$pdo->query('SELECT a.*,u.name user_name FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 100')->fetchAll();
    View::render('crud/audit',['title'=>'Audit Aktivitas','rows'=>$rows]); exit;
}
if ($module === 'akun') {
    Auth::requireAdmin();
    if ($action === 'index') {
        $rows=$pdo->query('SELECT id,name,email,role,is_active,created_at FROM users ORDER BY is_active DESC,name')->fetchAll();
        View::render('users/index',['title'=>__('Pengelolaan Akun','Account Management'),'rows'=>$rows]); exit;
    }
    if (in_array($action,['create','edit'],true)) {
        $row=[]; if($action==='edit'){$stmt=$pdo->prepare('SELECT id,name,email,role,is_active FROM users WHERE id=?');$stmt->execute([$id]);$row=$stmt->fetch()?:[];}
        View::render('users/form_two_roles',['title'=>$action==='create'?__('Daftarkan Akun','Register Account'):__('Ubah Akun','Edit Account'),'row'=>$row,'id'=>$id]); exit;
    }
    if ($action === 'save' && $method === 'POST') {
        Csrf::verify(); $id=(int)($_POST['id']??0); $name=trim((string)($_POST['name']??'')); $email=strtolower(trim((string)($_POST['email']??''))); $role=(string)($_POST['role']??'user'); $password=(string)($_POST['password']??''); $active=isset($_POST['is_active'])?1:0;
        if($name===''||!filter_var($email,FILTER_VALIDATE_EMAIL)||!in_array($role,['admin','user'],true)||(!$id&&strlen($password)<8)){flash('error',__('Periksa nama, email, peran, dan kata sandi minimal 8 karakter.','Check the name, email, role, and password of at least 8 characters.'));redirect('akun/'.($id?'edit/'.$id:'create'));}
        try{if($id){$sql='UPDATE users SET name=?,email=?,role=?,is_active=?,updated_at=CURRENT_TIMESTAMP';$args=[$name,$email,$role,$active];if($password!==''){$sql.=',password_hash=?';$args[]=password_hash($password,PASSWORD_DEFAULT);}$sql.=' WHERE id=?';$args[]=$id;}else{$sql='INSERT INTO users(name,email,password_hash,role,is_active) VALUES(?,?,?,?,?)';$args=[$name,$email,password_hash($password,PASSWORD_DEFAULT),$role,$active];}$stmt=$pdo->prepare($sql);$stmt->execute($args);$id=$id?: (int)$pdo->lastInsertId();Auth::audit($id?'update':'create','users',$id,'Account management');flash('success',__('Akun berhasil disimpan.','Account saved successfully.'));}catch(Throwable $e){flash('error',__('Email sudah digunakan.','Email is already in use.'));}redirect('akun');
    }
    if($action==='toggle'&&$method==='POST'){Csrf::verify();if($id===(int)Auth::user()['id']){flash('error',__('Anda tidak dapat menonaktifkan akun sendiri.','You cannot deactivate your own account.'));redirect('akun');}$stmt=$pdo->prepare('UPDATE users SET is_active=CASE is_active WHEN 1 THEN 0 ELSE 1 END,updated_at=CURRENT_TIMESTAMP WHERE id=?');$stmt->execute([$id]);Auth::audit('toggle','users',$id,'Account status');flash('success',__('Status akun diperbarui.','Account status updated.'));redirect('akun');}
    http_response_code(404); View::render('crud/error',['title'=>'404','message'=>__('Tindakan tidak tersedia.','Action unavailable.')]); exit;
}
if (!isset($configs[$module])) { http_response_code(404); View::render('crud/error',['title'=>'Tidak ditemukan','message'=>'Halaman yang Anda cari tidak tersedia.']); exit; }
$config=$configs[$module];

foreach (($config['selects']??[]) as $field=>$source) {
    if (is_array($source) && isset($source['table'])) {
        $label=$source['table']==='assets'?'name':'name';
        $config['options'][$field]=$pdo->query("SELECT id,$label name FROM {$source['table']}" . ($source['table']==='users'?' WHERE is_active=1':'') . " ORDER BY name")->fetchAll();
    } else $config['options'][$field]=array_map(fn($v)=>['id'=>$v,'name'=>$v],$source);
}

if ($action==='index') {
    $q=trim((string)($_GET['q']??'')); $where=[];$params=[];
    if(isset($config['type'])){$where[]='x.type=?';$params[]=$config['type'];}
    if($module==='tugas'){$where[]="x.status<>'Menunggu Verifikasi'";if(!Auth::isAdmin()){$where[]="(x.status='Tersedia' OR x.assignee_id=?)";$params[]=Auth::user()['id'];}}
    if($q!==''){$searchField=$module==='tugas'?'x.title':($module==='pemeriksaan'?'x.findings':'x.name');$where[]="($searchField LIKE ?)";$params[]="%$q%";}
    $sql="SELECT x.*";
    if($config['table']==='assets')$sql.=',l.name location,c.name category'.($module==='pc'?',p.pc_name,p.ip_address,p.ram_gb,p.os_version':'');
    if($config['table']==='tasks')$sql.=',u.name assignee';
    $sql.=" FROM {$config['table']} x";
    if($config['table']==='assets')$sql.=' LEFT JOIN locations l ON l.id=x.location_id LEFT JOIN asset_categories c ON c.id=x.category_id'.($module==='pc'?' LEFT JOIN pc_details p ON p.asset_id=x.id':'');
    if($config['table']==='tasks')$sql.=' LEFT JOIN users u ON u.id=x.assignee_id';
    if($where)$sql.=' WHERE '.implode(' AND ',$where); $sql.=' ORDER BY x.id DESC';
    $stmt=$pdo->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();
    View::render('crud/index',['title'=>$config['title'],'rows'=>$rows,'module'=>$module,'config'=>$config,'q'=>$q]); exit;
}
if ($action==='create'||$action==='edit') {
    if(!Auth::isAdmin()&&!in_array($module,['pc','perangkat','software'],true)){Auth::requireAdmin();} $row=[];
    if($action==='edit'){$sql="SELECT x.*".($module==='pc'?',p.pc_name,p.ip_address,p.processor,p.ram_gb,p.storage_type,p.storage_value,p.os,p.os_version':'')." FROM {$config['table']} x".($module==='pc'?' LEFT JOIN pc_details p ON p.asset_id=x.id':'')." WHERE x.id=?";$stmt=$pdo->prepare($sql);$stmt->execute([$id]);$row=$stmt->fetch()?:[];}
    View::render('crud/form',['title'=>($action==='create'?'Tambah ':'Ubah ').$config['title'],'row'=>$row,'module'=>$module,'config'=>$config,'id'=>$id]);exit;
}
if ($action==='save' && $method==='POST') {
    if(!Auth::isAdmin()&&!in_array($module,['pc','perangkat','software'],true)){Auth::requireAdmin();}Csrf::verify();$id=(int)($_POST['id']??0);$data=[];$errors=[];
    foreach($config['fields'] as $field=>$label){$value=trim((string)($_POST[$field]??''));if(in_array($field,['code','name','title','number','event_date','inspected_at'],true)&&$value==='')$errors[]="$label wajib diisi.";$data[$field]=$value===''?null:$value;}
    if(isset($data['quantity']) && (!is_numeric($data['quantity'])||(int)$data['quantity']<1))$errors[]='Jumlah minimal 1.';
    if($module==='pc'&&$id===0&&!preg_match('/^[A-Z0-9-]+$/',(string)$data['code']))$errors[]='Kode aset hanya menggunakan huruf besar, angka, dan tanda hubung.';
    if($errors){flash('error',implode(' ',$errors));$_SESSION['_old']=$data;redirect($module.'/'.($id?'edit/'.$id:'create'));}
    $pcData=[];if($module==='pc'){foreach(['pc_name','ip_address','processor','ram_gb','storage_type','storage_value','os','os_version'] as $key){$pcData[$key]=$data[$key]??null;unset($data[$key]);}}
    if(isset($config['type']))$data['type']=$config['type'];
    if($module==='pemeriksaan')$data['inspector_id']=Auth::user()['id'];
    try{$pdo->beginTransaction();if($id){$set=implode(',',array_map(fn($k)=>"$k=?",array_keys($data)));$stmt=$pdo->prepare("UPDATE {$config['table']} SET $set".(in_array($config['table'],['assets','software','tasks','events'],true)?',updated_at=CURRENT_TIMESTAMP':'').' WHERE id=?');$stmt->execute([...array_values($data),$id]);$verb='update';}else{$cols=implode(',',array_keys($data));$marks=implode(',',array_fill(0,count($data),'?'));$stmt=$pdo->prepare("INSERT INTO {$config['table']}($cols) VALUES($marks)");$stmt->execute(array_values($data));$id=(int)$pdo->lastInsertId();$verb='create';}if($module==='pc'){$exists=(int)$pdo->query('SELECT COUNT(*) FROM pc_details WHERE asset_id='.(int)$id)->fetchColumn();if($exists){$set=implode(',',array_map(fn($k)=>"$k=?",array_keys($pcData)));$stmt=$pdo->prepare("UPDATE pc_details SET $set WHERE asset_id=?");$stmt->execute([...array_values($pcData),$id]);}else{$cols=implode(',',array_keys($pcData));$marks=implode(',',array_fill(0,count($pcData),'?'));$stmt=$pdo->prepare("INSERT INTO pc_details(asset_id,$cols) VALUES(?,$marks)");$stmt->execute([$id,...array_values($pcData)]);}}Auth::audit($verb,$config['table'],$id,$config['title']);$pdo->commit();flash('success','Data berhasil disimpan.');}catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error','Data gagal disimpan. Pastikan kode, nama komputer, atau IP tidak duplikat.');}
    if($module==='software'&&$id){$stmt=$pdo->prepare('SELECT * FROM software WHERE id=?');$stmt->execute([$id]);$sw=$stmt->fetch();if($sw&&in_array($sw['status'],['Perlu Install','Perlu Dihapus'],true)){$kind=$sw['status']==='Perlu Install'?'INSTALL':'REMOVE';$number='SW-'.str_pad((string)$id,3,'0',STR_PAD_LEFT).'-'.$kind;$title=($kind==='INSTALL'?'Install ':'Hapus ').$sw['name'];$due=$kind==='INSTALL'?($sw['starts_at']??null):($sw['ends_at']??null);$check=$pdo->prepare("SELECT COUNT(*) FROM tasks WHERE software_id=? AND status NOT IN ('Selesai','Dibatalkan')");$check->execute([$id]);if(!(int)$check->fetchColumn()){$pdo->prepare("INSERT INTO tasks(number,title,description,category,priority,status,due_at,software_id,created_by) VALUES(?,?,?,?,?,'Tersedia',?,?,?)")->execute([$number,$title,'Tugas otomatis dari status software: '.$sw['status'],'Software','Sedang',$due,$id,Auth::user()['id']]);}}elseif($sw){$pdo->prepare("UPDATE tasks SET status='Dibatalkan',updated_at=CURRENT_TIMESTAMP WHERE software_id=? AND status NOT IN ('Selesai','Dibatalkan')")->execute([$id]);}}
    redirect($module);
}
if ($action==='delete' && $method==='POST') { Auth::requireAdmin();Csrf::verify();$stmt=$pdo->prepare("DELETE FROM {$config['table']} WHERE id=?");try{$stmt->execute([$id]);Auth::audit('delete',$config['table'],$id,$config['title']);flash('success','Data berhasil dihapus.');}catch(Throwable $e){flash('error','Data memiliki riwayat dan tidak dapat dihapus.');}redirect($module); }
if ($module==='tugas'&&$action==='ambil'&&$method==='POST') { Csrf::verify();$stmt=$pdo->prepare("UPDATE tasks SET assignee_id=?,status='Dikerjakan',taken_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP WHERE id=? AND status='Tersedia' AND assignee_id IS NULL");$stmt->execute([Auth::user()['id'],$id]);flash($stmt->rowCount()?'success':'error',$stmt->rowCount()?'Tugas berhasil diambil.':'Tugas sudah diambil pengguna lain.');redirect('tugas'); }
http_response_code(404);View::render('crud/error',['title'=>'Tidak ditemukan','message'=>'Tindakan tidak tersedia.']);

