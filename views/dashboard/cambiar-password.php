<?php include_once __DIR__."/header-dashboard.php";?>

<div class="contenedor-sm">
    <?php include_once __DIR__."/../templates/alertas.php";?>
    <a href="/perfil" class="enlace">Volver a Perfil</a>
    <form method="POST" action="/cambiar-password" class="formulario">
        <div class="campo">
            <label for="password_actual">Contraseña actual</label>
            <input type="password" name="password_actual" placeholder="Tu Contraseña Actual" id="password_actual">
        </div>
        <div class="campo">
            <label for="password_nuevo">Nueva contraseña</label>
            <input type="password" name="password_nuevo" placeholder="Tu Contraseña Nueva" id="password_nuevo">
        </div>
        <input type="submit" value="Guardar Cambios">
    </form>
</div>

<?php include_once __DIR__."/footer-dashboard.php";?>