<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

use Simit\Core\Sql;
use Simit\Policies\{AccountPolicy,AttachmentPolicy,ModulePolicy,ProfilePolicy,TaskPolicy};

$assert=function(bool $condition,string $message):void {
    if(!$condition){fwrite(STDERR,$message."\n");exit(1);}
};

$admin=['id'=>1,'role'=>'admin'];
$user=['id'=>2,'role'=>'user'];
$other=['id'=>3,'role'=>'user'];
$assigned=['assignee_id'=>2,'created_by'=>1,'is_contributor'=>0];
$otherAssigned=['assignee_id'=>3,'created_by'=>1,'is_contributor'=>0];
$shared=['assignee_id'=>null,'created_by'=>1,'is_contributor'=>0];
$contributed=['assignee_id'=>3,'created_by'=>1,'is_contributor'=>1];

$assert(TaskPolicy::view($user,$assigned),'PIC cannot view assigned task.');
$assert(!TaskPolicy::view($user,$otherAssigned),'User can view another user assigned task.');
$assert(TaskPolicy::view($user,$shared),'User cannot view shared task.');
$assert(TaskPolicy::view($user,$contributed),'Contributor cannot view contributed task.');
$assert(!TaskPolicy::submit($user,$otherAssigned),'User can submit another user task.');
$assert(TaskPolicy::approve($admin) && !TaskPolicy::approve($user),'Task approval role is incorrect.');
$assert(AttachmentPolicy::download($user,$assigned+['uploader_id'=>3]),'PIC cannot download task attachment.');
$assert(!AttachmentPolicy::download($user,$otherAssigned+['uploader_id'=>3]),'User can download unrelated attachment.');
$assert(ModulePolicy::update($user,'pc') && !ModulePolicy::delete($user,'pc'),'Module policy is incorrect.');
$assert(AccountPolicy::manage($admin) && !AccountPolicy::manage($user),'Account policy is incorrect.');
$assert(ProfilePolicy::update($user,2) && !ProfilePolicy::update($user,3),'Profile ownership policy is incorrect.');
$assert(Sql::identifier('created_at',['id','created_at'])==='created_at','Allowed sort column was rejected.');
$assert(Sql::direction('sideways')==='DESC','Invalid sort direction was not normalized.');

$rejected=false;
try{Sql::identifier('id DESC; DROP TABLE users',['id']);}catch(InvalidArgumentException){$rejected=true;}
$assert($rejected,'Unsafe SQL identifier was accepted.');

$vacuumPath=tempnam(sys_get_temp_dir(),'simit-vacuum-');
if($vacuumPath===false){fwrite(STDERR,"Could not allocate VACUUM test path.\n");exit(1);}
unlink($vacuumPath);
$memory=new PDO('sqlite::memory:');
$vacuum=$memory->prepare('VACUUM INTO ?');
$vacuum->execute([$vacuumPath]);
$assert(is_file($vacuumPath),'SQLite VACUUM filename binding failed.');
unlink($vacuumPath);

echo "Policy and SQL identifier tests passed.\n";
