<?php

/**
 *
 * cmBB [Español] - Traducido al Español por DiegoPino
 *
 * @copyright (c) 2016 Ger Bruinsma
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACL_U_CMBB_POST_ARTICLE'	 => 'Puede Postear cmBB Articulos',
	'ARTICLE_HIDDEN_WARNING'	 => 'Este Articulo esta Oculto y esta Accesible solo para Moderadores',
	'BACK'						 => 'Atraz',
	'CATEGORY'					 => 'Categoria',
	'CMBB_UPLOAD_BROWSE'		 => 'O Buscador',
	'CMBB_UPLOAD_DRAG'			 => 'Copia y Pega los Archivos aquí',
	'CMBB_UPLOAD_EXPLAIN'		 => 'Subir archivos atravez del Box Abajo. <br /> Tipos de archivo permitidos: ',
	'COMMENTS'					 => 'Ver Comentarios',
	'COMMENTS_DISABLED'			 => 'Comentarios Deshabilitados',
	'CONTENT'					 => 'Contenido del Articulo',
	'DELETE_ARTICLE'			 => 'Ocultar Articulo',
	'EDIT_ARTICLE'				 => 'Editar Articulo',
	'ERROR_MUCH_REMOVED'		 => 'Se ha Eliminado bastante de este Articulo. Esto podría ser un error del usuario abusivo o simples. Datos no Estan Guardados.',
	'ERROR_TITLE'				 => 'El Titulo Entrado No es Permitido.',
	'LOG_ARTICLE_VISIBILLITY'	 => 'Cambia Visibilidad del Articulo',
	'NEW_ARTICLE'				 => 'Nuevo Articulo',
	'NO_HIDDEN'					 => 'No Ocultar Articulos',
	'NO_REACTIONS_ARTICLE'		 => 'Desactivar los Comentarios <small>(comentarios podrían estar desactivados atravez de la categoría)</small>',
	'READ_MORE'					 => 'Leer Mas...',
	'RESTORE_ARTICLE'			 => 'Restaurar Articulo',
	'SHOW_HIDDEN'				 => 'Mostrar Articulos Ocultos',
	'TITLE'						 => 'Titulo',
	'WELCOME_USER'				 => 'Hola %s!',
		));
