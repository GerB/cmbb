<?php

/**
 *
 * cmBB [Dutch]
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
	'ACP_CATEGORIES_MANAGE'				=> 'Beheer categoriën',
	'ACP_CATEGORIES_MANAGE_EXPLAIN'		=> 'Hier kun je categoriën toevoegen, aanpassen of verwijderen. Maak eerst een categorie aan, vervolgens kun je de instellingen aanpassen voor de nieuwe categorie.'
											. '<br />Let op: cmBB gebruikt de categorienaam voor de URL. Als je eenmaal de categorienaam hebt gekozen is het daarom niet verstandig deze aan te passen omdat de URL vervolgens NIET meer wordt aangepast.',
	'ACP_CMBB_CATEGORIES'				=> 'Categoriën',
	'ACP_CMBB_SETTING_SAVED'			=> 'cmBB instellingen opgeslagen',
	'ACP_CMBB_TITLE'					=> 'cmBB',
	'ACP_MIN_TITLE_LENGTH'				=> 'Minumumlengte titel',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'		=> 'Vereiste minimumlengte voor de titel van een artikel',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Minumumlengte inhoud',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'	=> 'Vereiste minimumlengte voor de inhoud van een artikel. Voorkomt onzin artikelen.',
	'ACP_NO_ARTICLES'					=> 'Je hebt geen (actieve) artikelen. Maak een nieuw artikel aan via onderstaande link:',
	'ACP_NUMBER_INDEX_ITEMS'			=> 'Aantal indexitems',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'	=> 'Maximum aantal artikelen dat op de indexpagina (homepage) getoond wordt. Artikelen worden gesorteerd op datum (laatste artikel bovenaan).',
	'ACP_REACT_FORUM_ID'				=> 'Forum voor reacties.',
	'ACP_REACT_FORUM_ID_EXPLAIN'		=> 'Selecteer het forum waar onderwerpen voor reacties op de artikelen worden geplaatst.',
	'ACP_SHOW_MENUBAR'					=> 'Toon menubalk',
	'ACP_SHOW_MENUBAR_EXPLAIN'			=> 'Maak een menubalk aan in de header. Deze bevat alle categoriën waar artikelen in staat, plus de homepage (indien ingesteld), de forumindex en het contactformulier (indien ingesteld).',
	'ACP_SHOW_RIGHTBAR'					=> 'Toon rechter zijbalk',
	'ACP_SHOW_RIGHTBAR_EXPLAIN'			=> 'Je kunt er voor kiezen om aan de rechterkant van cmBB pagina\'s een zijbalk toe te voegen. De inhoud hiervan kun je zelf bepalen. Zinvol voor advertenties of eender welke content je wil tonen.',
	'ACP_RIGHTBAR_HTML'					=> 'Inhoud voor rechter zijbalk',
	'ACP_RIGHTBAR_HTML_EXPLAIN'			=> 'Indien de rechter zijbalk is geactiveerd kun je de inhoud daarvan hier bepalen. Je kunt elke vorm van HTML5 of Javascript gebruiken, zorg er wel voor dat het valideert.',
	'CHILDREN'							=> 'Onderliggend',
	'CHILDREN_EXPLAIN'					=> 'Aantal artikelen in deze categorie',
	'CMBB_CATEGORY_NAME_INVALID'		=> 'Categorie naam ongeldig',
	'CMBB_SETTINGS'						=> 'cmBB instellingen',
	'CMBB_DELETE_CAT_EXPLAIN'			=> 'Een categorie kan alleen verwijderd worden indien er geen artikelen in geplaatst zijn.',
	'CREATE_CATEGORY'					=> 'Categorie toevoegen',
	'ERROR_CATEGORY_NOT_EMPTY'			=> 'Categorie is niet leeg',
	'ERROR_FAILED_DELETE'				=> 'Verwijderen mislukt.',
	'NO_REACTIONS'						=> 'Reacties uitschakelen',
	'PROTECTED'							=> 'Beschermd',
	'PROTECTED_EXPLAIN'					=> 'Alleen moderators mogen artikelen in deze categorie plaatsen.',
	'SHOW_MENU_BAR'						=> 'Toon in menubalk',
	'SHOW_MENU_BAR_EXPLAIN'				=> 'Deze categorie wel of niet tonen in de menubalk (alleen indien er artikelen in geplaatst zijn). Zinvol om uit te schakelen indien je de categorieweergave niet praktisch vind of als je slechts enkele losse artikelen wil plaatsen.',

		));
