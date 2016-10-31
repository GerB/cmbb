<?php

/**
 *
 * cmBB [Español]  Traducido por DiegoPino
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
	'ACP_ANNOUNCE_TEXT'					=> 'Anuncio de texto',
	'ACP_ANNOUNCE_TEXT_EXPLAIN'			=> 'Texto que se mostrará sobre todos los artículos y artículos de la categoría. El BBCode no esta soportado, usa HTML.',
	'ACP_ANNOUNCE_SHOW'					=> 'Mostrar anuncio de texto',
	'ACP_ANNOUNCE_SHOW_EXPLAIN'			=> 'Seleccione "Si" o "No" si desea o no mostrar el texto proporcionado anteriormente',
	'ACP_CATEGORIES_MANAGE'				=> 'Administrar categorías',
	'ACP_CATEGORIES_MANAGE_EXPLAIN'		=> 'Aquí puede Añadir, Modificar o Borrar categorías. Puede crear primero una categoría y envíarla, después puede cambiar la configuración.'
											. '<br />Nota: cmBB utiliza el nombre de categoría de la URL. Una vez que haya elegido un nombre de categoría, se recomienda NO cambiar el nombre después ya que la URL NO se cambiará en consecuencia..',
	'ACP_CMBB_CATEGORIES'				=> 'Categorías',
	'ACP_CMBB_SETTING_SAVED'			=> 'Configuración de cmBB guardada',
	'ACP_CMBB_TITLE'					=> 'cmBB',
	'ACP_MIN_TITLE_LENGTH'				=> 'Longitud mínima del título',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'		=> 'Longitud mínima requerida de los títulos de los artículos',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Longitud mínima del contenido',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'	=> 'Longitud mínima requerida del contenido del artículo (cuerpo). Evita los artículos incomprensibles',
	'ACP_NUMBER_INDEX_ITEMS'			=> 'Número de elementos en el índice',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'	=> 'Número máximo de elementos más recientes que se mostrarán en la página de índice. Los artículos se ordenan por fecha (el último en la parte superior)',
	'ACP_REACT_FORUM_ID'				=> 'Foro para temas de comentarios',
	'ACP_REACT_FORUM_ID_EXPLAIN'		=> 'Seleccione el foro para crear un tema para comentarios.',
	'ACP_SHOW_MENUBAR'					=> 'Mostrar barra de menú',
	'ACP_SHOW_MENUBAR_EXPLAIN'			=> 'La barra de menús se agrega a la cabecera, contiene todas las categorías con los hijos junto a la página principal (de cualquier), página principal y contactanos (si está habilitado)',
	'ACP_SHOW_RIGHTBAR'					=> 'Mostrar barra lateral derecha',
	'ACP_SHOW_RIGHTBAR_EXPLAIN'			=> 'Puede elegir mostrar una barra lateral a la derecha, puede usar cuálquier contenido en HTML que desee. Útil para anuncios o cualquier otro contenido que desee mostrar.',
	'ACP_RIGHTBAR_HTML'					=> 'Contenido de la barra lateral derecha.',
	'ACP_RIGHTBAR_HTML_EXPLAIN'			=> 'Si tiene activada la barra lateral derecha, se mostrará el contenido introducido aquí. Puede utilizar cualquier código HTML/JS que desee, sólo asegúrese de que es válido..',
	'CHILDREN'							=> 'Hijo',
	'CHILDREN_EXPLAIN'					=> 'Número de artículos en esta categoría',
	'CMBB_SETTINGS'						=> 'Configuración de cmBB',
	'CMBB_DELETE_CAT_EXPLAIN'			=> 'Una categoría sólo se puede eliminar si esta no contiene artículos',
	'CREATE_CATEGORY'					=> 'Añadir categoría',
	'ERROR_FAILED_DELETE'				=> 'No se pudo eliminar.',
	'NO_REACTIONS'						=> 'desactivar los comentarios',
	'PROTECTED'							=> 'Protegido',
	'PROTECTED_EXPLAIN'					=> 'Solamente los moderadores pueden escribir comentarios',
	'SHOW_MENU_BAR'						=> 'Mostrar en la barra de menú',
	'SHOW_MENU_BAR_EXPLAIN'				=> 'Si desea o no mostrar esta categoría en la barra de menús (sólo cuando tiene hijos). Útil para deshabilitar si no te gustan los listados de categorías o tienes sólo algunos artículos sueltos..',

));
