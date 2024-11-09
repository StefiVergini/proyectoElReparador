function confirmDelete($msg){
    let response = confirm($msg);
    //return response? true:false;
    
    if(response){
        return true;
    }else{
        return false;
    }
}