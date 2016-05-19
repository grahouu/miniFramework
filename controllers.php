<?php

function login_action() {
  require 'views/login.php';
}

function home_action() {
  $users = get_users();
  require 'views/home.php';
}

function choose_list_action() {
  require 'views/choose_list.php';
}

//-------- UTILISATEURS -------

function list_users_action() {
  $users = get_users();
  require 'views/users/list.php';
}

function show_account_action($id) {
  $user = get_user_by_id($id);
  require 'views/users/show.php';
}

function administration_action() {
  require 'views/administration.php';
}

//-------------------------------------------------------------- SERVEURS ---------

function list_servers_action() {
  $servers = get_servers();
  require 'views/servers/list.php';
}

function show_server_action($id) {
  $server = get_server_by_id($id);
  require 'views/servers/show.php';
}

function new_server_action() {
  require 'views/servers/new.php';
}

function create_server_action($attributes) {
  $server = create_server($attributes);
  if ($server == "false") {
    echo "Le serveur existe deja !";
  } else {
    header('Location: show?id='.$server->id);
  }
  
}

function edit_server_action($id) {
  $server=get_server_by_id($id);
  require 'views/servers/edit.php';
}

function update_server_action($id, $attributes) {
  $server=update_server_by_id($id, $attributes);
  header('Location: show?id='.$id);
}

function delete_server_action($id){
  delete_server_by_id($id);
  header('Location: list');
}

function constraint_days_action(){
  require 'views/servers/constraint_days.php';
}

function update_constraint_days_action($days){
  update_constraint_days($days);
  require 'views/administration.php';
}

//----------------------------------------------------------------- CONTROLES ---------

function choose_control_action() {
  require 'views/choose_control.php';
}

function list_controls_action($tri=null) {
  $controls = get_controls($tri);
  require 'views/controls/list.php';
}

function new_control_action($type) {
  $structures_controls=get_structures_controls();
  switch ($type) {
    case 'morning':
      $tasks= $structures_controls["morning"];
      require 'views/controls/new_morning.php';
      break;
    case 'afternoon':
      $tasks= $structures_controls["afternoon"];
      require 'views/controls/new_afternoon.php';
      break;
    case 'server':
      $tasks= $structures_controls["server"];
      $servers=get_servers();
      $server_controls_by_date_control=get_servers_by_date_control("done");
      $server_controls_by_date_control_todo=get_servers_by_date_control("todo");
      require 'views/controls/new_server.php';
      break;
    case 'scheduled_tasks':
      $tasks= $structures_controls["scheduled_tasks"];
      $server_by_name= get_server_by_name("s19");
      require 'views/controls/new_scheduled_tasks.php';
      break;
    //TODO à compléter
    default:
      
      break;
  }
}

function edit_control_action($params) {
  $control = get_control_by_id($params["id"]);
  $tasks = get_tasks_by_control_id($params["id"]);  
  $structures_controls=get_structures_controls();
  switch ($params["type"]) {
    case 'morning':
      $type_tasks= $structures_controls["morning"];
      require 'views/controls/edit.php';
      break;
    case 'afternoon':
      $type_tasks= $structures_controls["afternoon"];
      require 'views/controls/edit.php';
      break;
    case 'server':
      $type_tasks= $structures_controls["server"];
      require 'views/controls/edit.php';
      break;
    case 'scheduled_tasks':
      $type_tasks= $structures_controls["scheduled_tasks"];
      require 'views/controls/edit.php';
      break;
  }
}

function update_control_action($id,$attributes) {
  $control = update_control_by_id($id,$attributes);
  if ($control == false) {
    echo "Erreur lors de la mise à jour du controle";
  } else {
    header('Location: show?id='.$control->id);
  }
}

function create_control_action($attributes) {
  $control = create_control($attributes);
  if($control)
    header('Location: show?id='.$control->id);
  else
    echo "Erreur d'enregistrement";
}

function show_control_action($id) {
  $control = get_control_by_id($id);
  $tasks = get_tasks_by_control_id($id);
  $user = get_user_by_id($control->user_id);
  $structures_controls=get_structures_controls();
  switch ($control->type) {
  case 'morning':
    $type_tasks = $structures_controls["morning"];
    require 'views/controls/show_morning.php';
    break;
  case 'afternoon':
    $type_tasks = $structures_controls["afternoon"];
    require 'views/controls/show_afternoon.php';
    break;
  case 'server':
    $type_tasks = $structures_controls["server"];
    $server = get_server_by_id($control->server_id);
    require 'views/controls/show_server.php';
    break;
  case 'scheduled_tasks':
    $type_tasks = $structures_controls["scheduled_tasks"];
    require 'views/controls/show_scheduled_tasks.php';
    break;
  //TODO à compléter
  default:
    
    break;
  }
}

function show_control_last_id_action(){
  $id = get_control_max_id();
  show_control_action($id);
}

function delete_control_action($id){
  delete_control_by_id($id);
  header('Location: list');
}

?>
