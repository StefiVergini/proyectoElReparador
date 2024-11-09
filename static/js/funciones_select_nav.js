function handleSelectChange(select) {
    const selectedValue = select.value;
    if (selectedValue === "cerrar_sesion") {
      document.getElementById('selectCerrarSesion').submit();
    }else if (selectedValue === "cpass")  {
      document.getElementById('selectChange').submit();
    }else if (selectedValue === "perfil")  {
      document.getElementById('selectPerfil').submit();
    }
  }