<?php
header('Content-Type: application/json');
require_once '../lib/SkillRepository.php';
$r=new SkillRepository();
$db=$r->load();
$link=json_decode(file_get_contents('php://input'),true);
$db['links'][]=$link;
$db['links']=array_values(array_unique($db['links'],SORT_REGULAR));
$r->save($db);
echo json_encode(['status'=>'ok']);
