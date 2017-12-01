<?php

/**
 *
 * cmBB [English]
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
	'ACP_CATEGORIES_MANAGE'				=> 'Gérer les catégories',
	'ACP_CATEGORIES_MANAGE_EXPLAIN'		=> 'Ici vous pouvez ajouter, modifier ou supprimer des catégories. Vous devez en premier créer une catégorie et la soumettre, puis changer les réglages.'
											. '<br />A noter : cmBB utilise le nom de catégorie pour l’URL. Une fois que vous avez choisi un nom de catégorie, il est donc recommandé de ne PAS changer le nom par la suite puisque l’URL ne sera pas modifiée en conséquence.',
	'ACP_CMBB_CATEGORIES'				=> 'Catégories',
	'ACP_CMBB_SETTING_SAVED'			=> 'Paramètres de cmBB sauvegardés',
	'ACP_CMBB_TITLE'					=> 'cmBB',
	'ACP_MIN_TITLE_LENGTH'				=> 'Longueur de titre minimum',
	'ACP_MIN_TITLE_LENGTH_EXPLAIN'		=> 'Longueur minimale requise des titres d’articles',
	'ACP_MIN_CONTENT_LENGTH'			=> 'Longueur minimum du contenu',
	'ACP_MIN_CONTENT_LENGTH_EXPLAIN'	=> 'Longueur minimale requise du contenu de l’article (corps). Empêche les articles de charabia.',
	'ACP_NO_ARTICLES'					=> 'Vous n’avez aucuns articles (actifs). Créez un nouvel article en utilisant le lien ci-dessous :',
	'ACP_NUMBER_INDEX_ITEMS'			=> 'Nombre d’articles sur l’index',
	'ACP_NUMBER_INDEX_ITEMS_EXPLAIN'	=> 'Nombre maximum des derniers articles à afficher sur la page d’index. Les articles sont triés par date (dernier en haut)',
	'ACP_REACT_FORUM_ID'				=> 'Forum pour les commentaires',
	'ACP_REACT_FORUM_ID_EXPLAIN'		=> 'Sélectionnez le forum où créer les sujets pour les commentaires.',
	'ACP_SHOW_MENUBAR'					=> 'Afficher la barre de menu',
	'ACP_SHOW_MENUBAR_EXPLAIN'			=> 'La barre de menu est ajoutée au header, contient toutes les catégories contenant des articles ainsi que la page d’accueil du site (le cas échéant), l’index du forum et la page Nous Contacter (si activée).',
	'ACP_SHOW_RIGHTBAR'					=> 'Afficher la barre latérale de droite',
	'ACP_SHOW_RIGHTBAR_EXPLAIN'			=> 'Vous pouvez choisir d’afficher une barre latérale à droite contenant le code HTML que vous voulez. Utile pour les publicités ou tout autre contenu que vous pourriez vouloir montrer.',
	'ACP_RIGHTBAR_HTML'					=> 'Contenu de la barre latérale de droite',
	'ACP_RIGHTBAR_HTML_EXPLAIN'			=> 'Si vous avez activé la barre latérale de droite, le contenu entré ici s’affichera. Vous pouvez utiliser n’importe quel HTML/JS, assurez-vous seulement qu’il soit valide.',
	'CHILDREN'							=> 'Articles',
	'CHILDREN_EXPLAIN'					=> 'Nombre d’articles dans cette catégorie',
	'CMBB_CATEGORY_NAME_INVALID'		=> 'Nom de catégorie invalide',
	'CMBB_SETTINGS'						=> 'Paramètres cmBB',
	'CMBB_DELETE_CAT_EXPLAIN'			=> 'Une catégorie peut seulement être supprimée quand elle ne contient plus d’articles',
	'CREATE_CATEGORY'					=> 'Ajouter une catégorie',
	'ERROR_CATEGORY_NOT_EMPTY'			=> 'Catégorie non vide',
	'ERROR_FAILED_DELETE'				=> 'Echec de la suppression.',
	'NO_REACTIONS'						=> 'Désactiver les commentaires',
	'PROTECTED'							=> 'Protégé',
	'PROTECTED_EXPLAIN'					=> 'Seuls les modérateurs sont autorisés à poster',
	'SHOW_MENU_BAR'						=> 'Afficher dans la barre de menu',
	'SHOW_MENU_BAR_EXPLAIN'				=> 'Afficher ou non cette catégorie dans la barre de menu (uniquement si elle a des articles) Utile pour désactiver si vous n’aimez pas le listing des catégories ou si vous avez juste quelques articles en vrac.',

		));
