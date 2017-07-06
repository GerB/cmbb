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
	'ACL_U_CMBB_POST_ARTICLE'	 => 'Puede postear artículos',
	'ARTICLES'					 => 'Artículos',
	'ARTICLES_TOTAL'			 => 'Algunos artículos',
	'ARTICLE_HIDDEN_WARNING'	 => 'Este artículo esta oculto y esta accesible solo para Moderadores',
	'BACK'						 => 'Atrás',
	'CATEGORY'					 => 'Categoría',
	'CMBB_UPLOAD_BROWSE'		 => 'O búscar',
	'CMBB_UPLOAD_DRAG'			 => 'Copía y pega los archivos aquí',
	'CMBB_UPLOAD_EXPLAIN'		 => 'Subir los archivos a través de la caja de abajo. <br /> Tipos de archivo permitidos: ',
	'COMMENTS'					 => 'Ver comentarios',
	'COMMENTS_DISABLED'			 => 'Comentarios deshabilitados',
	'CONTENT'					 => 'Contenido del artículo',
	'DELETE_ARTICLE'			 => 'Ocultar artículo',
	'EDIT_ARTICLE'				 => 'Editar artículo',
	'ERROR_MUCH_REMOVED'		 => 'Se ha eliminado bastante de este artículo. Esto puede ser un error abusivo o simple del usuario. Los datos no se guardarán.',
	'ERROR_TITLE'				 => 'El título proporcionado no está permitido.',
	'LOG_ARTICLE_VISIBILLITY'	 => 'Cambia la visibilidad del artículo',
	'NEW_ARTICLE'				 => 'Nuevo Articulo',
	'NO_HIDDEN'					 => 'No ocultar los articulos',
	'NO_REACTIONS_ARTICLE'		 => 'Desactivar los comentarios <small>(Los comentarios podrían estar desactivados a través de la configuración de categorías)</small>',
	'READ_MORE'					 => 'Leer Más...',
	'RESTORE_ARTICLE'			 => 'Restaurar articulo',
	'SEARCH_USER_ARTICLES'		 => 'Buscar en los artículos del usuario',
	'SHOW_HIDDEN'				 => 'Mostrar artículos ocultos',
	'TITLE'						 => 'Título',
	'WELCOME_USER'				 => 'Hola %s!',
));
