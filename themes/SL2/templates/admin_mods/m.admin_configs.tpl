<div class="boxy-title">
    <h3>Caracter&iacute;sticas y Opciones</h3>
</div>
<div id="res" class="boxy-content">
{if $tsSave}<div class="alert alert-info">Configuraciones guardadas</div>{/if}
	<form action="" method="post" autocomplete="off">
    <fieldset>
        <legend>Configuraci&oacute;n del Sitio</legend>
        <dl>
            <dt><label for="ai_titulo">Nombre del Sitio:</label></dt>
            <dd><input type="text" class="form-control" id="ai_titulo" name="titulo" maxlength="24" value="{$tsConfig.titulo}" /></dd>
        </dl>
        <dl>
            <dt><label for="ai_slogan">Descripci&oacute;n del Sitio:</label></dt>
            <dd><input type="text" class="form-control" id="ai_slogan" name="slogan" maxlength="32" value="{$tsConfig.slogan}"/></dd>
        </dl>
        <dl>
            <dt><label for="ai_url">Direcci&oacute;n del sitio:</label></dt>
            <dd><input type="text" class="form-control" id="ai_url" name="url" maxlength="32" value="{$tsConfig.url}" /></dd>
        </dl>
         <dl>
            <dt><label for="ai_email">Email del sitio:</label></dt>
            <dd><input type="text" class="form-control" id="ai_email" name="email" value="{$tsConfig.email}" /></dd>
         </dl>
         <dl>
            <dt><label for="ai_idioma">Seleccione idioma {$tsConfig.idioma}:</label><br /><span>Por el momento es para el servidor de PHP y que muestre las fechas en el idioma seleccionado.</span></dt>
            <dd>
               <select id="ai_idioma" class="form-select" name="idioma">
                  <option value="es_ES"{if $tsConfig.idioma == 'es_ES'} selected{/if}>Español</option>
                  <option value="en_EN"{if $tsConfig.idioma == 'en_EN'} selected{/if}>English</option>
               </select>
            </dd>
         </dl>
        <hr />
        <dl>
            <dt><label for="pkey">reCaptcha p&uacute;blica</label><span>Clave p&uacute;blica de <a href="https://www.google.com/recaptcha/admin">reCatpcha</a>.</span></dt>
            <dd><input type="text" class="form-control" id="pkey" name="pkey" value="{$tsConfig.pkey}" /></dd>
        </dl>
        <dl>
            <dt><label for="skey">reCaptcha secreta</label><span>Clave privada de <a href="https://www.google.com/recaptcha/admin">reCatpcha</a>.</span></dt>
            <dd><input type="text" class="form-control" id="skey" name="skey" value="{$tsConfig.skey}" /></dd>
        </dl>
        <hr />
        <dl>
            <dt><label for="ai_offline">Modo mantenimiento:</label><span>Esto har&aacute; al Sitio inaccesible a los usuarios. Si quiere, tambi&eacute;n puede introducir un breve mensaje (255 caracteres) para mostrar.</span></dt>
            <dd>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="offline" id="offline1" value="1"{if $tsConfig.offline == 1} checked{/if}>
                    <label class="form-check-label" for="offline1">S&iacute;</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="offline" id="offline2" value="1"{if $tsConfig.offline == 0} checked{/if}>
                    <label class="form-check-label" for="exampleRadios2">No</label>
                </div>
                <textarea name="offline_message" class="form-control" placeholder="Mensaje que se mostrará en mantenimiento" id="ai_offline" style="height: 120px;" >{$tsConfig.offline_message}</textarea>
            </dd>
        </dl>
        <hr />
        <dl>
            <dt><label for="ai_edad">Edad requerida:</label> <span>A partir de que edad los usuarios pueden registrarse.</span></dt>
            <dd><input type="text" class="form-control" id="ai_edad" name="c_allow_edad" style="width:10%" maxlength="2" value="{$tsConfig.c_allow_edad}" /> a&ntilde;os.</dd>
        </dl>
		 <dl>
            <dt><label for="ai_met_welcome">Mensaje de Bienvenida:</label><span id="desc_message_welcome"  {if $tsConfig.c_met_welcome == 0}style="display:none;"{/if}> <br /> [usuario] => Nombre del registrado <br /> [welcome] => Bienvenido/a depende del sexo <br /> [web] => Nombre de esta web <br /> <br />(Se aceptan BBCodes y Smileys)</span></dt>
            <dd>
			<select id="ai_met_welcome" name="c_met_welcome" class="form-select" {if $tsConfig.c_met_welcome == 0} onchange="if($('#ai_met_welcome').val() != 0) $('textarea[name=message_welcome]').slideDown(); $('#desc_message_welcome').slideDown();" {/if}>
    			<option value="0"{if $tsConfig.c_met_welcome == 0} selected{/if}>No dar bienvenida</option>
                <option value="1"{if $tsConfig.c_met_welcome == 1} selected{/if}>Muro</option>
                <option value="2"{if $tsConfig.c_met_welcome == 2} selected{/if}>Mensaje privado</option>
                <option value="3"{if $tsConfig.c_met_welcome == 3} selected{/if}>Aviso</option>
            </select>
			<br />
            <textarea name="c_message_welcome" class="form-control" id="ai_met_welcome" style="height: 100px; {if $tsConfig.c_met_welcome == 0} display:none; {/if}" >{$tsConfig.c_message_welcome}</textarea>
            </dd>
        </dl>
        <dl>
            <dt><label for="ai_active">Usuario online:</label><span>Tiempo que debe trascurrir para considerar que un usuario est&aacute; en linea.</span></dt>
            <dd><input type="text" class="form-control" id="ai_active" name="c_last_active" style="width:10%" maxlength="2" value="{$tsConfig.c_last_active}" /> min.</dd>
        </dl>
        <dl>
            <dt><label for="ai_stats_cache">Estad&iacute;sticas en buffer:</label><span>Tiempo que debe trascurrir para actualizar las estad&iacute;sticas del sitio.</span></dt>
            <dd><input type="text" class="form-control" id="ai_stats_cache" name="c_stats_cache" style="width:10%" maxlength="2" value="{$tsConfig.c_stats_cache}" /> min.</dd>
        </dl>
        <dl>
            <dt><label for="ai_sess_ip">Login por IP:</label><span>Por seguridad cada que un usuario cambie de IP se le pedir&aacute; loguearse nuevamente.</span></dt>
            <dd>
                <label><input name="c_allow_sess_ip" type="radio" id="ai_sess_ip" value="1" {if $tsConfig.c_allow_sess_ip == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_allow_sess_ip" type="radio" id="ai_sess_ip" value="0" {if $tsConfig.c_allow_sess_ip != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
        <dl>
		    <dt><label for="ai_count_guests">Los visitantes suman estad&iacute;sticas</label><span>Contar a los visitantes en las estad&iacute;sticas generales.</span></dt>                                          
            <dd>
		     <label><input name="c_count_guests" type="radio" id="ai_count_guests" value="1" {if $tsConfig.c_count_guests == 1} checked{/if} class="radio"/>S&iacute;</label>                                              
		     <label><input name="c_count_guests" type="radio" id="ai_count_guests" value="0" {if $tsConfig.c_count_guests != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
		
		<dl>
		    <dt><label for="ai_hits_guest">Los visitantes suman visitas</label><span>Contar las visitas de los visitantes en posts y fotos.</span></dt>                                          
            <dd>
		     <label><input name="c_hits_guest" type="radio" id="ai_hits_guest" value="1" {if $tsConfig.c_hits_guest == 1} checked{/if} class="radio"/>S&iacute;</label>                                              
		     <label><input name="c_hits_guest" type="radio" id="ai_hits_guest" value="0" {if $tsConfig.c_hits_guest != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>

        <hr />
        <dl>
            <dt><label for="ai_reg_active">Registro abierto:</label><span>Permitir el registro de nuevos usuarios</span></dt>
            <dd>
                <label><input name="c_reg_active" type="radio" id="ai_reg_active" value="1" {if $tsConfig.c_reg_active == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_reg_active" type="radio" id="ai_reg_active" value="0" {if $tsConfig.c_reg_active != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
        <dl>
            <dt><label for="ai_reg_activate">Activar usuarios:</label><span>Activar autom&aacute;ticamente la cuenta de usuario.</span></dt>
            <dd>
                <label><input name="c_reg_activate" type="radio" id="ai_reg_activate" value="1" {if $tsConfig.c_reg_activate == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_reg_activate" type="radio" id="ai_reg_activate" value="0" {if $tsConfig.c_reg_activate != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
		
		<dl>
		
            <dt><label for="ai_firma">Firma de usuario:</label><span>Las firmas de los usuarios son visibles en los post.</span></dt>
			<dd>
                <label><input name="c_allow_firma" type="radio" id="ai_firma" value="1" {if $tsConfig.c_allow_firma == 1} checked{/if} class="radio"/>S&iacute;</label>
				<label><input name="c_allow_firma" type="radio" id="ai_firma" value="0" {if $tsConfig.c_allow_firma != 1} checked{/if} class="radio"/>No</label>
			</dd>
		</dl>
		
        <hr />
        <dl>
            <dt><label for="ai_upload">Carga externa:</label><span>Si cuentas con un servidor de pago o la librer&iacute;a CURL puedes subir im&aacute;genes remotamente a imgur.com</span></dt>
            <dd>
                <label><input name="c_allow_upload" type="radio" id="ai_upload" value="1" {if $tsConfig.c_allow_upload == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_allow_upload" type="radio" id="ai_upload" value="0" {if $tsConfig.c_allow_upload != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
		
        <dl>
            <dt><label for="ai_portal">Activar portal:</label><span>Los usuarios podr&aacute;n tener un inicio perzonalizado.</span></dt>
            <dd>
                <label><input name="c_allow_portal" type="radio" id="ai_portal" value="1" {if $tsConfig.c_allow_portal == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_allow_portal" type="radio" id="ai_portal" value="0" {if $tsConfig.c_allow_portal != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
		
		<dl>
		    <dt><label for="ai_fotos_private">Secci&oacute;n de fotos oculta</label><span>Si est&aacute; activado, los visitantes no podr&aacute;n ver la secci&oacute;n fotos.</span></dt>                                            
            <dd>
		      <label><input name="c_fotos_private" type="radio" id="ai_fotos_private" value="1" {if $tsConfig.c_fotos_private == 1} checked{/if} class="radio"/>S&iacute;</label>                                               
		      <label><input name="c_fotos_private" type="radio" id="ai_fotos_private" value="0" {if $tsConfig.c_fotos_private != 1} checked{/if} class="radio"/>No</label>                                            
            </dd>
        </dl>
		
		<dl>
		    <dt><label for="ai_see_mod">Vista moderativa amplia</label><span>Si est&aacute; activado, el equipo de moderaci&oacute;n podr&aacute; ver, diferenciado por colores, los distintos estados de los posts.</span></dt>                                            
            <dd>
		      <label><input name="c_see_mod" type="radio" id="ai_see_mod" value="1" {if $tsConfig.c_see_mod == 1} checked{/if} class="radio"/>S&iacute;</label>                                               
		      <label><input name="c_see_mod" type="radio" id="ai_see_mod" value="0" {if $tsConfig.c_see_mod != 1} checked{/if} class="radio"/>No</label>                                            
            </dd>
        </dl>
		
		<dl>
		    <dt><label for="ai_desapprove_post">Revisi&oacute;n de posts tras su publicaci&oacute;n</label><span>Si est&aacute; activado, el equipo de moderaci&oacute;n deber&aacute; aprobar un post antes de que &eacute;ste sea publicado.</span></dt>                                            
            <dd>
		      <label><input name="c_desapprove_post" type="radio" id="ai_desapprove_post" value="1" {if $tsConfig.c_desapprove_post == 1} checked{/if} class="radio"/>S&iacute;</label>                                               
		      <label><input name="c_desapprove_post" type="radio" id="ai_desapprove_post" value="0" {if $tsConfig.c_desapprove_post != 1} checked{/if} class="radio"/>No</label>                                            
            </dd>
        </dl>
		
        <hr />
		<dl>
            <dt><label for="ai_keep_points">Mantener los puntos:</label><span>Al momento de recargar los puntos, si est&aacute; habilitado se conservar&aacute;n los puntos que el usuario no haya gastado los puntos en el d&iacute;, si est&aacute; deshabilitado, se restablecer&aacute;n a los puntos asignados para cada rango.</span></dt>
            <dd>
                <label><input name="c_keep_points" type="radio" id="ai_keep_points" value="1" {if $tsConfig.c_keep_points == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_keep_points" type="radio" id="ai_keep_points" value="0" {if $tsConfig.c_keep_points != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
        <dl>
            <dt><label for="ai_live">Notificaciones Live:</label><span>Los usuarios podr&aacute;n ver en tiempo real sus notificaciones. (Esta opci&oacute;n puede consumir un poco m&aacute;s de recursos.)</span></dt>
            <dd>
                <label><input name="c_allow_live" type="radio" id="ai_live" value="1" {if $tsConfig.c_allow_live == 1} checked{/if} class="radio"/>S&iacute;</label>
                <label><input name="c_allow_live" type="radio" id="ai_live" value="0" {if $tsConfig.c_allow_live != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
        <dl>
            <dt><label for="ai_max_nots">M&aacute;ximo de notificaciones:</label><span>Cuantas notificaciones puede recibir un usuario.</span></dt>
            <dd><input type="text" class="form-control" id="ai_max_nots" name="c_max_nots" style="width:10%" maxlength="3" value="{$tsConfig.c_max_nots}" /></dd>
        </dl>
        <dl>
            <dt><label for="ai_max_acts">M&aacute;ximo de actividades:</label><span>Cuantas actividades puede registrar un usuario.</span></dt>
            <dd><input type="text" class="form-control" id="ai_max_acts" name="c_max_acts" style="width:10%" maxlength="3" value="{$tsConfig.c_max_acts}" /></dd>
        </dl>
        <hr />
        <dl>
            <dt><label for="ai_max_post">Posts por p&aacute;gina:</label><span>N&uacute;mero m&aacute;ximo de posts a mostrar en cada p&aacute;gina de la portada.</span></dt>
            <dd><input type="text" class="form-control" id="ai_max_post" name="c_max_posts" style="width:10%" maxlength="3" value="{$tsConfig.c_max_posts}" /></dd>
        </dl>
        <dl>
            <dt><label for="ai_max_com">Comentarios por post:</label><span>N&uacute;mero m&aacute;ximo de comentarios por p&aacute;gina en los post.</span></dt>
            <dd><input type="text" class="form-control" id="ai_max_com" name="c_max_com" style="width:10%" maxlength="3" value="{$tsConfig.c_max_com}" /></dd>
        </dl>
		<dl>
            <dt><label for="ai_allow_points" class="qtip" title="Si introducimos '0', se permitir&aacute; dar los puntos definidos por el rango del usuario.  <br />  Si introducimos '-1', se podr&aacute;n dar todos los puntos que el usuario tenga para dar hoy. <br /> Introduciendo un n&uacute;mero superior a 0, todos los usuarios sin importar su rango, tend&aacute;n esa cantidad para dar." >Puntos por post:</label><br /><span>N&uacute;mero m&aacute;ximo de puntos que permitimos dar en los posts. </span></dt>
            <dd><input type="text" class="form-control" id="ai_allow_points" name="c_allow_points" style="width:10%" maxlength="3" value="{$tsConfig.c_allow_points}" /></dd>
        </dl>
        <dl>
            <dt><label for="ai_sum_p">Los votos suman puntos:</label><br /><span>Cada voto positivo en un comentario es un punto m&aacute;s para el usuario. <strong>Nota:</strong> Los votos negativos no restan puntos</span></dt>
            <dd>
                <label><input name="c_allow_sump" type="radio" id="ai_sum_p" value="1" {if $tsConfig.c_allow_sump == 1} checked{/if} class="radio"/>Si</label>
                <label><input name="c_allow_sump" type="radio" id="ai_sum_p" value="0" {if $tsConfig.c_allow_sump != 1} checked{/if} class="radio"/>No</label>
            </dd>
        </dl>
        <hr />
        <dl>
            <dt><label for="ai_nfu">Cambio de rango:</label><span>Un usuario sube de rango cuando obtiene los puntos m&iacute;nimos en:</span></dt>
            <dd>
                <label><input name="c_newr_type" type="radio" id="ai_nfu" value="1" {if $tsConfig.c_newr_type == 1} checked{/if} class="radio"/>Todos sus post</label>
                <label><input name="c_newr_type" type="radio" id="ai_nfu" value="0" {if $tsConfig.c_newr_type != 1} checked{/if} class="radio"/>Solo en un post</label>
            </dd>
        </dl>
        <p><input type="submit" name="save" value="Guardar Cambios" class="btn btn-success"/></p>
    </fieldset>
    </form>
</div>