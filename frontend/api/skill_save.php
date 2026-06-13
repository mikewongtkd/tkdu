<?php
header('Content-Type: application/json');
require_once '../lib/SkillRepository.php';
$r=new SkillRepository();
$db=$r->load();
$skill=json_decode(file_get_contents('php://input'),true);
$found=false;
foreach($db['skills'] as &$s){
 if($s['uuid']==$skill['uuid']){$s=$skill;$found=true;}
}
if(!$found)$db['skills'][]=$skill;
$r->save($db);
echo json_encode(['status'=>'ok']);
