<div class="content-tabs perfil">
	<fieldset>
	   <div class="alert-cuenta cuenta-2"></div>
	   <div class="form-group">
	      <label class="form-label" for="nombre">Nombre completo</label>
	      <input type="text" value="{$tsPerfil.p_nombre}" maxlength="60" name="nombre" id="nombre" class="form-control">
	   </div>
	   <div class="form-group">
	      <label class="form-label" for="sitio">Mensaje Personal</label>
	      <textarea value="" maxlength="60" name="mensaje" id="mensaje" class="form-control">{$tsPerfil.p_mensaje}</textarea>
	   </div>
	   <div class="form-group">
	      <label class="form-label" for="sitio">Sitio Web</label>
	      <input type="text" value="{$tsPerfil.p_sitio}" maxlength="60" name="sitio" id="sitio" class="form-control">
	   </div>
   <div class="form-group">
      <label class="form-label" for="ft">Redes sociales</label>
      <div class="red-group">
         {foreach $tsRedes key=name item=red}
	         <div class="input-group flex-nowrap">
				  	<span class="input-group-text" id="iconred"><iconify-icon icon="{$red.iconify}"></iconify-icon></span>
				  	<input type="text" class="form-control" placeholder="{$red.nombre}" aria-label="{$red.nombre}" name="red[{$name}]" aria-describedby="iconred" value="{$tsPerfil.p_socials.$name}">
	            {if $name == 'discord'}
	            	<small onclick="explicacion();return false;" style="top: 6px;right: 5px;" class="position-absolute icono" title="Ajustes de usuario > Copiar ID"><iconify-icon icon="flat-color-icons:info"></iconify-icon></small>
	            {/if}
				</div>
         {/foreach}
       </div>
   </div>
	   <div class="form-group">
	      <label class="form-label" for="estado">Estado Civil</label>
	      <select class="form-select cuenta-save-2" name="estado" id="estado">
	         {foreach from=$tsPData.estado key=val item=text}
	            <option value="{$val}" {if $tsPerfil.p_estado == $val}selected="selected"{/if}>{$text}</option>
	         {/foreach}
	      </select>
	   </div>
	   <div class="d-flex justify-content-center align-items-center">
	      <input type="button" value="Guardar y seguir" onclick="cuenta.guardar_datos()" class="btn btn-success">
	   </div>
	</fieldset>
</div>