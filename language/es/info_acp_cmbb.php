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
	'ACP_ANNOUNCE_TEXT'					=> 'Auncio de texto',
	'ACP_ANNOUNCE_TEXT_EXPLAIN'			=> 'Texto que se Mostrara por encima de todos articulos y categorias. No se analiza BBCoDE, usa HTML.',
	'ACP_ANNOUNCE_SHOW'					=> 'Mostrar Anuncio de Texto',
	'ACP_ANNOUNCE_SHOW_EXPLAIN'			=> 'Seleccione "Si" o "No"Wether para Mostrar el texto proporcionado por encima de',
	'ACP_CATEGORIES_MANAGE'				=> 'Administrar Categorias',
	'ACP_CATEGORIES_MANAGE_EXPLAIN'		=> 'Aca puede Añadir, Modificar o Borrar Categorias. Puede Crear Primero una Categoria y Enviarla, Despues Puede Cambiar la Configuracion.'
											. '<br />Nota: cmBB Usa la Categoria para la URL. Una vez creado el Nombre de Categoria, NO es Recomendable cambiar el Nombre porque la URL toma la Categoria de nombre. no se modificará en consecuencia.',
	'ACP_CMBB_CATEGORIES'				=> 'Categorias',
	'ACP_CMBB_SETTING_SAVED'			=> 'cmBB Configuracion Salvada',
	'ACP_CMBB_TITLE'					=> 'cmBB',
	'ACP_MIN_TITLE_LENGTH'				=> 'Minimo del Titulo Requerido',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'		=> 'Requiere un minimo longitud de títulos de los artículos',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Contenido Minimo',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'	=> 'Requiere un minimo de longitud en Contenido de Articulo (body). .',
	'ACP_NUMBER_INDEX_ITEMS'			=> 'Número de índice',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'	=> 'El número máximo de los últimos artículos para mostrar en la página de índice. Los elementos se ordenan por fecha (último en la parte superior)',
	'ACP_REACT_FORUM_ID'				=> 'Foro para temas de comentario',
	'ACP_REACT_FORUM_ID_EXPLAIN'		=> 'Selecciona el foro para crear un tema en los comentarios.',
	'ACP_SHOW_MENUBAR'					=> 'Mostrar barra de menú',
	'ACP_SHOW_MENUBAR_EXPLAIN'			=> 'Barra de menú se agrega al (Header), contiene todas las categorías, board index and contact us (if enabled).',
	'ACP_SHOW_RIGHTBAR'					=> 'Mostrar barra lateral derecha',
	'ACP_SHOW_RIGHTBAR_EXPLAIN'			=> 'Puede optar por mostrar una barra lateral a la derecha, contendico con cualquier HTML que desea. Útil para los anuncios o cualquier otro tipo de contenido es posible que desee mostrar.',
	'ACP_RIGHTBAR_HTML'					=> 'Contenido de la barra lateral derecha.',
	'ACP_RIGHTBAR_HTML_EXPLAIN'			=> 'Si tiene habilitada la barra lateral derecha, El contenido aca sera Mostrado. Puede Usar Cualquier codigo HTML/JS que desee, sólo asegúrese de que es válida.',
	'CHILDREN'							=> 'Children',
	'CHILDREN_EXPLAIN'					=> 'Número de artículos en esta categoría',
	'CMBB_SETTINGS'						=> 'cmBB Configuración',
	'CMBB_DELETE_CAT_EXPLAIN'			=> 'Una Categoria puede ser Borrada cuando no tenga Articulos',
	'CREATE_CATEGORY'					=> 'Añadir Categoria',
	'ERROR_FAILED_DELETE'				=> 'No se pudo eliminar.',
	'NO_REACTIONS'						=> 'desactivar los comentarios',
	'PROTECTED'							=> 'Protegido',
	'PROTECTED_EXPLAIN'					=> 'Solamente los moderadores pueden escribir comentarios',
	'SHOW_MENU_BAR'						=> 'Mostrar en la barra de menú',
	'SHOW_MENU_BAR_EXPLAIN'				=> 'Ya sea o no que se muestran esta categoría en la barra de menú (sólo cuando tiene children). Útil para desactivar si no te gusta las listas de categorías o si tiene sólo algunos artículos sueltos.',

		));
