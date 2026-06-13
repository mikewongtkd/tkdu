<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Skill Graph Manager</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-chart-graph/build/index.umd.min.js"></script>
<style>
.skill{border:1px solid #ccc;padding:6px;margin:4px;border-radius:4px;background:#f8f9fa}
.children{margin-left:25px}
</style>
</head>
<body class="container-fluid p-3">
<div class="d-flex gap-2 mb-3">
<button class="btn btn-primary" id="newSkill"><i class="fa fa-plus"></i> Skill</button>
<button class="btn btn-secondary" id="treeBtn">Tree</button>
<button class="btn btn-secondary" id="graphBtn">Graph</button>
<a class="btn btn-success" href="api/export.php">Export</a>
</div>

<div class="row">
<div class="col-md-3">
<h5>Library</h5>
<div id="library"></div>
</div>
<div class="col-md-9">
<div id="treePanel"></div>
<canvas id="graphCanvas" style="display:none;height:700px"></canvas>
</div>
</div>

<div class="modal fade" id="skillModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5>Skill</h5></div>
<div class="modal-body">
<input id="uuid" type="hidden">
<div class="form-group">
  <label for="name">Name</label>
  <input id="name" class="form-control mb-2" placeholder="Name">
</div>
<div class="form-group">
  <label for="description">Description</label>
  <textarea id="description" class="form-control mb-2"></textarea>
</div>
<div class="form-group">
  <label for="difficulty">Difficulty</label>
  <input id="difficulty" type="number" class="form-control" value="0">
</div>
</div>
<div class="modal-footer">
<button class="btn btn-primary" id="saveSkill">Save</button>
<button class="btn btn-secondary" id="cancel" data-dismiss="modal">Cancel</button>
</div>
</div></div></div>

<script>
let db={skills:[],links:[]};
let chart=null;

function loadData(){
 $.getJSON('api/skills.php',d=>{db=d;renderLibrary();renderTree();});
 $( '.skill' ).each(( i, skill ) => {
  skill = $( skill );
  let uuid = skill.data( 'uuid' );
  new Sortable( skill, {
    group: 'nested',
    animation: 150,
    fallbackOnBody: true,
    swapThreshold: 0.65
  });
 });
}

function skillById(id){return db.skills.find(s=>s.uuid===id);}

function renderLibrary(){
 let h='';
 db.skills.forEach(s=>{
   h+=`<div class="skill" draggable="true" data-id="${s.uuid}">${s.name}<button class="btn btn-sm btn-outline-none float-end edit" data-id="${s.uuid}" style="margin-top: -0.25em;"><span class="fas fa-pencil"></span></button></div>`;
 });
 $('#library').html(h);
}

function roots(){
 let children=new Set(db.links.map(l=>l.child));
 return db.skills.filter(s=>!children.has(s.uuid));
}

function renderNode(id,path=[]){
 if(path.includes(id)) return '<div class="text-danger">Cycle Ref</div>';
 let s=skillById(id);
 if(!s) return '';
 let html=`<div class="skill" data-id="${id}">
 <b>${s.name}</b>
 <div>${s.description||''}</div>
 <div class="children">`;
 db.links.filter(l=>l.parent===id).forEach(l=>{
   html+=renderNode(l.child,[...path,id]);
 });
 html+='</div></div>';
 return html;
}

function renderTree(){
 let html='';
 roots().forEach(r=>html+=renderNode(r.uuid));
 $('#treePanel').html(html).show();
 $('#graphCanvas').hide();
}

function renderGraph(){
 $('#treePanel').hide();
 $('#graphCanvas').show();
 let ctx=document.getElementById('graphCanvas');
 if(chart) chart.destroy();
 chart=new Chart(ctx,{
   type:'graph',
   data:{datasets:[{
      data:db.skills.map(s=>({id:s.uuid,label:s.name})),
      edges:db.links.map(l=>({source:l.parent,target:l.child}))
   }]}
 });
}

$('#treeBtn').on('click',renderTree);
$('#graphBtn').on('click',renderGraph);

$('#newSkill').on('click',()=>{
 $('#uuid').val(crypto.randomUUID());
 $('#name').val('');
 $('#description').val('');
 $('#difficulty').val(0);
 new bootstrap.Modal(document.getElementById('skillModal')).show();
});

$(document).on('click','.edit',function(){
 let s=skillById($(this).data('id'));
 $('#uuid').val(s.uuid);
 $('#name').val(s.name);
 $('#description').val(s.description);
 $('#difficulty').val(s.difficulty);
 new bootstrap.Modal(document.getElementById('skillModal')).show();
});

$('#saveSkill').on('click',()=>{
 $.ajax({
  url:'api/skill_save.php',
  method:'POST',
  contentType:'application/json',
  data:JSON.stringify({
   uuid:$('#uuid').val(),
   name:$('#name').val(),
   description:$('#description').val(),
   difficulty:parseInt($('#difficulty').val())
  }),
  success:loadData
 });
});

loadData();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
