-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 22 Paź 2017, 18:39
-- Wersja serwera: 10.1.10-MariaDB
-- Wersja PHP: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `once`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_documentation`
--

CREATE TABLE `edit_documentation` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_faq`
--

CREATE TABLE `edit_faq` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `question` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `answer` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_frameworks`
--

CREATE TABLE `edit_frameworks` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(25) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_frameworks`
--

INSERT INTO `edit_frameworks` (`id`, `type_id`, `name`, `source`, `path`) VALUES
(1, 1, 'CodeIgniter', 'github.com/bcit-ci/CodeIgniter', ''),
(2, 1, 'Laravel', 'https://github.com/laravel/laravel', ''),
(3, 2, 'AngularJS', 'github.com/angular/angular.js/', ''),
(4, 2, 'Vue', 'github.com/vuejs/vue', ''),
(5, 3, 'Bootstrap', 'https://github.com/twbs/bootstrap', ''),
(6, 3, 'Foundation', 'http://foundation.zurb.com/sites/download.html/', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_langs`
--

CREATE TABLE `edit_langs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `name_id` varchar(11) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `source` text NOT NULL,
  `source_en` text NOT NULL,
  `source_pl` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_langs_categories`
--

CREATE TABLE `edit_langs_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_langs_types`
--

CREATE TABLE `edit_langs_types` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL DEFAULT '',
  `desc` varchar(55) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_langs_types`
--

INSERT INTO `edit_langs_types` (`id`, `name`, `desc`) VALUES
(1, 'en', 'English'),
(2, 'pl', 'Polish'),
(3, 'cs', 'Czech'),
(4, 'es', 'Spanish'),
(5, 'pt', 'Portuguese'),
(6, 'bg', 'Bulgarian'),
(7, 'zh', 'Chinese'),
(8, 'hr', 'Croatian'),
(9, 'da', 'Danish'),
(10, 'nl', 'Dutch'),
(11, 'fr', 'French'),
(12, 'fi', 'Finnish'),
(13, 'et', 'Estonian'),
(14, 'ab', 'Abkhaz'),
(15, 'aa', 'Afar'),
(16, 'af', 'Afrikaans'),
(17, 'ak', 'Akan'),
(18, 'sq', 'Albanian'),
(19, 'am', 'Amharic'),
(20, 'ar', 'Arabic'),
(21, 'an', 'Aragonese'),
(22, 'hy', 'Armenian'),
(23, 'as', 'Assamese'),
(24, 'av', 'Avaric'),
(25, 'ae', 'Avestan'),
(26, 'ay', 'Aymara'),
(27, 'az', 'Azerbaijani'),
(28, 'bm', 'Bambara'),
(29, 'ba', 'Bashkir'),
(30, 'eu', 'Basque'),
(31, 'be', 'Belarusian'),
(32, 'bn', 'Bengali'),
(33, 'bh', 'Bihari'),
(34, 'bi', 'Bislama'),
(35, 'bs', 'Bosnian'),
(36, 'br', 'Breton'),
(37, 'my', 'Burmese'),
(38, 'ca', 'Catalan'),
(39, 'ch', 'Chamorro'),
(40, 'ce', 'Chechen'),
(41, 'ny', 'Chichewa'),
(42, 'cv', 'Chuvash'),
(43, 'kw', 'Cornish'),
(44, 'co', 'Corsican'),
(45, 'cr', 'Cree'),
(46, 'dv', 'Divehi'),
(47, 'dz', 'Dzongkha'),
(48, 'eo', 'Esperanto'),
(49, 'ee', 'Ewe'),
(50, 'fo', 'Faroese'),
(51, 'fj', 'Fijian'),
(52, 'ff', 'Fula'),
(53, 'gl', 'Galician'),
(54, 'ka', 'Georgian'),
(55, 'de', 'German'),
(56, 'el', 'Greek'),
(57, 'gn', 'Guaraní'),
(58, 'gu', 'Gujarati'),
(59, 'ht', 'Haitian'),
(60, 'ha', 'Hausa'),
(61, 'he', 'Hebrew'),
(62, 'hz', 'Herero'),
(63, 'hi', 'Hindi'),
(64, 'ho', 'Hiri Motu'),
(65, 'hu', 'Hungarian'),
(66, 'ia', 'Interlingua'),
(67, 'id', 'Indonesian'),
(68, 'ie', 'Interlingue'),
(69, 'ga', 'Irish'),
(70, 'ig', 'Igbo'),
(71, 'ik', 'Inupiaq'),
(72, 'io', 'Ido'),
(73, 'is', 'Icelandic'),
(74, 'it', 'Italian'),
(75, 'iu', 'Inuktitut'),
(76, 'ja', 'Japanese'),
(77, 'jv', 'Javanese'),
(78, 'kl', 'Kalaallisut'),
(79, 'kn', 'Kannada'),
(80, 'kr', 'Kanuri'),
(81, 'ks', 'Kashmiri'),
(82, 'kk', 'Kazakh'),
(83, 'km', 'Khmer'),
(84, 'ki', 'Kikuyu'),
(85, 'rw', 'Kinyarwanda'),
(86, 'ky', 'Kyrgyz'),
(87, 'kv', 'Komi'),
(88, 'kg', 'Kongo'),
(89, 'ko', 'Korean'),
(90, 'ku', 'Kurdish'),
(91, 'kj', 'Kwanyama'),
(92, 'la', 'Latin'),
(93, 'lb', 'Luxembourgish'),
(94, 'lg', 'Ganda'),
(95, 'li', 'Limburgish'),
(96, 'ln', 'Lingala'),
(97, 'lo', 'Lao'),
(98, 'lt', 'Lithuanian'),
(99, 'lu', 'Luba-Katanga'),
(100, 'lv', 'Latvian'),
(101, 'gv', 'Manx'),
(102, 'mk', 'Macedonian'),
(103, 'mg', 'Malagasy'),
(104, 'ms', 'Malay'),
(105, 'ml', 'Malayalam'),
(106, 'mt', 'Maltese'),
(107, 'mi', 'Māori'),
(108, 'mr', 'Marathi'),
(109, 'mh', 'Marshallese'),
(110, 'mn', 'Mongolian'),
(111, 'na', 'Nauru'),
(112, 'nv', 'Navajo'),
(113, 'nb', 'Norwegian Bokmål'),
(114, 'nd', 'North Ndebele'),
(115, 'ne', 'Nepali'),
(116, 'ng', 'Ndonga'),
(117, 'nn', 'Norwegian Nynorsk'),
(118, 'no', 'Norwegian'),
(119, 'ii', 'Nuosu'),
(120, 'nr', 'South Ndebele'),
(121, 'oc', 'Occitan'),
(122, 'oj', 'Ojibwe'),
(123, 'cu', 'Old Church Slavonic'),
(124, 'om', 'Oromo'),
(125, 'or', 'Oriya'),
(126, 'os', 'Ossetian'),
(127, 'pa', 'Panjabi'),
(128, 'pi', 'Pāli'),
(129, 'fa', 'Persian'),
(130, 'ps', 'Pashto'),
(131, 'qu', 'Quechua'),
(132, 'rm', 'Romansh'),
(133, 'rn', 'Kirundi'),
(134, 'ro', 'Romanian'),
(135, 'ru', 'Russian'),
(136, 'sa', 'Sanskrit'),
(137, 'sc', 'Sardinian'),
(138, 'sd', 'Sindhi'),
(139, 'se', 'Northern Sami'),
(140, 'sm', 'Samoan'),
(141, 'sg', 'Sango'),
(142, 'sr', 'Serbian'),
(143, 'gd', 'Scottish Gaelic'),
(144, 'sn', 'Shona'),
(145, 'si', 'Sinhala'),
(146, 'sk', 'Slovak'),
(147, 'sl', 'Slovene'),
(148, 'so', 'Somali'),
(149, 'st', 'Southern Sotho'),
(150, 'su', 'Sundanese'),
(151, 'sw', 'Swahili'),
(152, 'ss', 'Swati'),
(153, 'sv', 'Swedish'),
(154, 'ta', 'Tamil'),
(155, 'te', 'Telugu'),
(156, 'tg', 'Tajik'),
(157, 'th', 'Thai'),
(158, 'ti', 'Tigrinya'),
(159, 'bo', 'Tibetan Standard'),
(160, 'tk', 'Turkmen'),
(161, 'tl', 'Tagalog'),
(162, 'tn', 'Tswana'),
(163, 'to', 'Tonga'),
(164, 'tr', 'Turkish'),
(165, 'ts', 'Tsonga'),
(166, 'tt', 'Tatar'),
(167, 'tw', 'Twi'),
(168, 'ty', 'Tahitian'),
(169, 'ug', 'Uyghur'),
(170, 'uk', 'Ukrainian'),
(171, 'ur', 'Urdu'),
(172, 'uz', 'Uzbek'),
(173, 've', 'Venda'),
(174, 'vi', 'Vietnamese'),
(175, 'vo', 'Volapük'),
(176, 'wa', 'Walloon'),
(177, 'cy', 'Welsh'),
(178, 'wo', 'Wolof'),
(179, 'wy', 'Western Frisian'),
(180, 'xh', 'Xhosa'),
(181, 'yi', 'Yiddish'),
(182, 'yo', 'Yoruba'),
(183, 'za', 'Zhuang'),
(184, 'zu', 'Zulu');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_layers`
--

CREATE TABLE `edit_layers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `default` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_layers_cols`
--

CREATE TABLE `edit_layers_cols` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `layer_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `col_id` int(11) NOT NULL DEFAULT '0',
  `row_id` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `css_id` varchar(55) NOT NULL DEFAULT '',
  `css_class` varchar(55) NOT NULL DEFAULT '',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `namespace` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_layers_rows`
--

CREATE TABLE `edit_layers_rows` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `layer_id` int(11) NOT NULL DEFAULT '0',
  `row_id` int(11) NOT NULL DEFAULT '0',
  `css_id` varchar(55) NOT NULL DEFAULT '',
  `css_class` varchar(55) NOT NULL DEFAULT '',
  `container` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_mailbox`
--

CREATE TABLE `edit_mailbox` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `user_id_to` int(11) NOT NULL DEFAULT '0',
  `author` varchar(32) NOT NULL DEFAULT '',
  `email` varchar(32) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `draft` tinyint(4) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_mailbox_contacts`
--

CREATE TABLE `edit_mailbox_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(32) NOT NULL DEFAULT '',
  `phone` varchar(15) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_mailbox_types`
--

CREATE TABLE `edit_mailbox_types` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `perm` int(11) NOT NULL DEFAULT '0',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `action` varchar(55) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_mailbox_types`
--

INSERT INTO `edit_mailbox_types` (`id`, `project_id`, `name`, `position`, `perm`, `ico`, `action`) VALUES
(1, 1, 'Inbox', 0, 0, 'fa fa-inbox', ''),
(2, 1, 'Sent', 0, 0, 'fa fa-mail-forward', ''),
(3, 1, 'Starred', 0, 0, 'fa fa-star', ''),
(4, 1, 'Junk', 0, 0, 'fa fa-folder', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_menu`
--

CREATE TABLE `edit_menu` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `url` varchar(55) NOT NULL DEFAULT '',
  `target` varchar(55) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_pages`
--

CREATE TABLE `edit_pages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `layer_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `source` text NOT NULL,
  `tags` text NOT NULL,
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '',
  `logged` tinyint(4) NOT NULL DEFAULT '0',
  `adult` tinyint(4) NOT NULL DEFAULT '0',
  `admins` tinyint(4) NOT NULL DEFAULT '0',
  `moderators` tinyint(4) NOT NULL DEFAULT '0',
  `users` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_pages_cols`
--

CREATE TABLE `edit_pages_cols` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `row_id` int(11) NOT NULL DEFAULT '0',
  `col_id` int(11) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `css_id` varchar(55) NOT NULL DEFAULT '',
  `css_class` varchar(55) NOT NULL DEFAULT '',
  `hidden` tinyint(4) NOT NULL DEFAULT '0',
  `namespace` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_pages_rows`
--

CREATE TABLE `edit_pages_rows` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `row_id` int(11) NOT NULL DEFAULT '0',
  `css_id` varchar(55) NOT NULL DEFAULT '',
  `css_class` varchar(55) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_pages_types`
--

CREATE TABLE `edit_pages_types` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `perm` int(11) NOT NULL DEFAULT '0',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `action` varchar(55) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_pages_types`
--

INSERT INTO `edit_pages_types` (`id`, `project_id`, `name`, `position`, `perm`, `ico`, `action`) VALUES
(1, 1, 'Published', 0, 0, 'fa fa-inbox', ''),
(2, 1, 'Drafts', 0, 0, 'fa fa-pencil-square-o', ''),
(3, 1, 'Starred', 0, 0, 'fa fa-star', ''),
(4, 1, 'Junk', 0, 0, 'fa fa-folder', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_plugins`
--

CREATE TABLE `edit_plugins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `tags` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(55) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `visits` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `price` float NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `reports` int(11) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Zrzut danych tabeli `edit_plugins`
--

INSERT INTO `edit_plugins` (`id`, `user_id`, `project_id`, `type_id`, `category_id`, `object_id`, `version`, `name`, `description`, `tags`, `author`, `author_url`, `visits`, `comments`, `price`, `downloads`, `votes`, `reports`, `stared`, `created`, `updated`, `published`) VALUES
(1, 1, 0, 0, 0, 1, 0, 'Once Snippets', 'The first OnceBuilder plugin that was made to speed up building web app with already existing html css and js code blocks! Feel free to send your opinion about Once project.', 'code blocks, code block builder, html blocks, css blocks, javascript, snippet, snippets, ready to use code, puzzle', '', 'oncebuilder.com', 0, 0, 0, 0, 0, 0, 0, 1505881045, 0, 1),
(2, 1, 0, 0, 0, 17, 0, 'Once Login', 'This module alows u put login window any where you wish! It works perfect with Once Register module.', 'php login, login window, login user, remind password, forget password', '', 'oncebuilder.com', 1, 0, 0, 0, 0, 0, 0, 1505881336, 0, 1),
(3, 1, 0, 0, 0, 18, 0, 'Once Signup', 'Click this module anywhere to setup your first or another signup page in 10 second guaranteed!', 'php register, php signup, register page, signup php, register php', '', 'oncebuilder.com', 1, 0, 0, 0, 0, 0, 0, 1505881555, 0, 1),
(4, 1, 0, 0, 0, 6, 0, 'Once Cookie Law', 'Cookie alert on EU', 'cookie law, eu cookie law, cookie plugin, cookie alert set it up in your page in less than 10s!', '', 'oncebuilder.com', 1, 0, 0, 0, 0, 0, 0, 1505881660, 0, 1);


--
-- Struktura tabeli dla tabeli `edit_plugins_categories`
--

CREATE TABLE `edit_plugins_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_plugins_categories`
--

INSERT INTO `edit_plugins_categories` (`id`, `project_id`, `name`, `ico`, `parent_id`, `level`, `position`) VALUES
(3, 1, 'Navigations', 'fa fa-bars', 0, 0, 1),
(4, 1, 'Sliders', 'fa fa-caret-square-o-right', 0, 0, 3),
(5, 1, 'Posts', 'fa fa-comment-o', 0, 0, 5),
(8, 1, 'Dialogs', 'fa fa-rss-square', 0, 0, 4),
(9, 1, 'Galleries', 'fa fa-picture-o', 0, 0, 7),
(11, 1, 'Users', 'fa fa-user', 0, 0, 9),
(12, 1, 'Faqs', 'fa fa-question', 0, 0, 6),
(13, 1, 'Documentations', 'fa fa-pencil-square', 0, 0, 10),
(14, 1, 'Googles', 'fa fa-google-plus', 0, 0, 11),
(15, 1, 'Newsletters', 'fa fa-envelope', 0, 0, 2),
(16, 1, 'Products', 'fa fa-shopping-cart', 0, 0, 12),
(17, 1, 'Tables', 'fa fa-table', 0, 0, 13),
(18, 1, 'Forms', 'fa fa-list-alt', 0, 0, 0),
(19, 1, 'Misc', 'fa fa-flask', 0, 0, 14);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_plugins_downloads`
--

CREATE TABLE `edit_plugins_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `mktime` tinyint(4) NOT NULL DEFAULT '0',
  `user_ip` varchar(15) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_plugins_reports`
--

CREATE TABLE `edit_plugins_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_plugins_visits`
--

CREATE TABLE `edit_plugins_visits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_plugins_votes`
--

CREATE TABLE `edit_plugins_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `plugin_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_posts`
--

CREATE TABLE `edit_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `layer_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(55) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `source` text NOT NULL,
  `tags` text NOT NULL,
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(4) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '',
  `logged` tinyint(4) NOT NULL DEFAULT '0',
  `adult` tinyint(4) NOT NULL DEFAULT '0',
  `all` tinyint(4) NOT NULL DEFAULT '0',
  `admins` tinyint(4) NOT NULL DEFAULT '0',
  `moderators` tinyint(4) NOT NULL DEFAULT '0',
  `users` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_posts_types`
--

CREATE TABLE `edit_posts_types` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `perm` int(11) NOT NULL DEFAULT '0',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_posts_types`
--

INSERT INTO `edit_posts_types` (`id`, `project_id`, `name`, `position`, `perm`, `ico`, `action`) VALUES
(1, 1, 'Published', 0, 0, 'fa fa-inbox', ''),
(2, 1, 'Drafts', 1, 0, 'fa fa-pencil-square-o', ''),
(3, 1, 'Pedding', 2, 0, 'fa fa-mail-forward', ''),
(4, 1, 'Starred', 3, 0, 'fa fa-star', ''),
(5, 1, 'Junk', 4, 0, 'fa fa-folder', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_referers`
--

CREATE TABLE `edit_referers` (
  `id` int(11) NOT NULL,
  `referer_id` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ' ',
  `referer_website` varchar(32) NOT NULL DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_routes`
--

CREATE TABLE `edit_routes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `name_id` varchar(11) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `source` text NOT NULL,
  `source_en` text NOT NULL,
  `source_pl` text NOT NULL,
  `source_cs` text NOT NULL,
  `source_es` text NOT NULL,
  `source_pt` text NOT NULL,
  `source_bg` text NOT NULL,
  `source_zh` text NOT NULL,
  `source_hr` text NOT NULL,
  `source_da` text NOT NULL,
  `source_aa` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_routes_categories`
--

CREATE TABLE `edit_routes_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '0',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_settings`
--

CREATE TABLE `edit_settings` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `login` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `api_key` varchar(40) NOT NULL DEFAULT '',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_settings_types`
--

CREATE TABLE `edit_settings_types` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `perm` tinyint(4) NOT NULL DEFAULT '0',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_settings_types`
--

INSERT INTO `edit_settings_types` (`id`, `project_id`, `name`, `position`, `perm`, `ico`, `action`) VALUES
(1, 1, 'General settings', 0, 0, 'fa fa-gear', ''),
(2, 1, 'Remote server', 0, 0, 'fa fa-location-arrow', ''),
(7, 1, 'Social media', 0, 0, 'fa fa-facebook-square', ''),
(5, 1, 'Database', 0, 0, 'fa fa-hdd-o', ''),
(6, 1, 'Languages', 0, 0, 'fa fa-globe', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets`
--

CREATE TABLE `edit_snippets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `licence` varchar(255) NOT NULL DEFAULT '',
  `visits` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `reports` int(11) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0',
  `route` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_snippets`
--

INSERT INTO `edit_snippets` (`id`, `user_id`, `project_id`, `type_id`, `category_id`, `object_id`, `version`, `name`, `description`, `tags`, `author`, `author_url`, `licence`, `visits`, `comments`, `downloads`, `votes`, `reports`, `stared`, `created`, `updated`, `published`, `route`, `file`, `source`) VALUES
(1, 1, 0, 0, 1, 1, 0, 'OnceBuilder Contact Form', '', 'bootstrap, bootstrap blocks, code snippets, code blocks, snippets, short codes, contact form, contact snippet', 'OnceBuilder', 'oncebuilder.com', '', 422, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(2, 1, 0, 0, 7, 2, 0, 'Responsive multi level menu', '', 'responsive, menu, navigation, navigation tabbed, jquery, css, rwd, multi level menu, responsive menu', 'OnceBuilder', 'oncebuilder.com', '', 405, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(3, 0, 0, 0, 17, 130, 0, 'Simple start template on bootstrap', '', 'Boostram template, animation, user interface,  bootstrap HTML, CSS, JS, code snippet', 'brojask', 'bootsnipp.com/brojask', '', 309, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(4, 0, 0, 0, 11, 60, 0, 'Bootstrap user profile', '', 'code snippets, bootstrap blocks, bootstrap, code blocks, snippets, bootstrap user profile, user profile, code snippets', 'mouse0270', 'twitter.com/mouse0270', '', 257, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(5, 0, 0, 0, 6, 157, 0, 'Login / registration form', '', 'login, forms, panel, registration,  bootstrap, registration form, login form', 'calvinko', 'bootsnipp.com/calvinko', '', 242, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(6, 0, 0, 0, 5, 797, 0, 'Bootstrap User List', '', 'user list, social list, bootstrap list, bootstrap user list', 'DesignBootstrap', 'desisnbootstrap.com', '', 258, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(7, 0, 0, 0, 1, 80, 0, 'Bootstrap buttons', '', 'buttons, menu, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Fstgeorge', 'bootsnipp.com/Fstgeorge', '', 292, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(8, 0, 0, 0, 11, 172, 0, 'Profile side bar menu', '', 'profile, sidebar, menu, user interface,  code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'keenthemes', 'bootsnipp.com/keenthemes', '', 412, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(9, 0, 0, 0, 9, 574, 0, 'Blog comments', '', 'bootstrap, snippet, blog,comments, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 320, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(10, 0, 0, 0, 17, 399, 0, 'Responsive video display', '', 'responsive, layouts, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'mrmccormack', 'bootsnipp.com/mrmccormack', '', 288, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(11, 0, 0, 0, 7, 35, 0, 'Admin toolbar menu', '', 'toolbar, admin, menu, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'msurguy', 'twitter.com/msurguy', '', 288, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(12, 0, 0, 0, 17, 591, 0, 'Bootstrap Simple 404 error page', '', 'page 404, 404 template, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 326, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(13, 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', 51, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(14, 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', 27, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(15, 0, 0, 0, 5, 647, 0, 'Table user list', '', 'bootstrap, snippet, table, user, list code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 320, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(16, 0, 0, 0, 1, 231, 0, 'Bootstrap calendar', '', 'jQuery plugin, user interface, calendar,  code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'mouse0270', 'twitter.com/mouse0270', '', 325, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(17, 0, 0, 0, 17, 117, 0, 'Bootstrap search bar', '', 'search, forms, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'animeshmanglik', 'twitter.com/animeshmanglik', '', 247, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(18, 0, 0, 0, 17, 270, 0, 'Form set search / icons / buttons', '', 'search, icon fonts, buttons', 'imjohnlouie', 'bootsnipp.com/imjohnlouie', '', 247, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(19, 0, 0, 0, 17, 239, 0, 'Shop cart list', '', 'shop, cms, shop cart, cart, bootstrap snippet', 'msurguy', 'twitter.com/msurguy', '', 254, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(20, 0, 0, 0, 10, 311, 0, 'Pricing table based on bootstrap', '', 'table, responsive, price table, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'outsideMyBox', 'bootsnipp.com/outsideMyBox', '', 336, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(21, 0, 0, 0, 6, 800, 0, 'Bootstrap login template', '', 'login box, login window, login page, login', 'DesignBootstrap', 'designbootstrap.com', '', 316, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(22, 0, 0, 0, 17, 230, 0, 'Material design icon with tittle', '', 'ico box, user interface, layouts, responsive, material design, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'eldragon87', 'twitter.com/eldragon87', '', 296, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(23, 0, 0, 0, 6, 421, 0, 'Bootstrap login panel', '', 'bootstrap, login panel, login box, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'jordi', 'bootsnipp.com/jordi', '', 282, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(24, 0, 0, 0, 16, 535, 0, 'Animated carousel', '', 'carousel, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BootsThemeClub', 'twitter.com/bootsthemeclub', '', 309, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(25, 0, 0, 0, 5, 434, 0, 'Admin panel mail box list', '', 'user interface, forms, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'hardiksondagar', 'twitter.com/hardiksondagar', '', 269, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(26, 0, 0, 0, 11, 428, 0, 'Small social user profile', '', 'social, user, profile', 'msurguy', 'twitter.com/msurguy', '', 235, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(27, 0, 0, 0, 15, 427, 0, 'Bootstrap payment form and checkout', '', 'payment, panel, forms, checkout', 'BhaumikPatel', 'twitter.com/patel0phone', '', 295, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(28, 0, 0, 0, 17, 504, 0, 'Buttons controls with transition', '', 'buttons, user, controls, boostrap buttons', 'msurguy', 'twitter.com/msurguy', '', 252, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(29, 0, 0, 0, 11, 498, 0, 'Responsive social card profile', '', 'responsive, profile, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'codename065', 'bootsnipp.com/codename065', '', 352, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(30, 0, 0, 0, 17, 497, 0, 'Media control buttons', '', 'buttons, user interface, media buttons, control buttons', 'mrmccormack', 'bootsnipp.com/mrmccormack', '', 204, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(31, 0, 0, 0, 5, 496, 0, 'Estate listing with image', '', 'lists, responsive, images, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'bmoeller1', 'moz.com/community/users/4158419', '', 735, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(32, 0, 0, 0, 17, 466, 0, 'Material design buttons on hover effect', '', 'material design, buttons', 'opensourcematters', 'bootsnipp.com/opensourcematters', '', 289, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(33, 0, 0, 0, 15, 465, 0, 'Payment cart forms', '', 'payment, panel, forms, checkout', 'iosdsv', 'bootsnipp.com/iosdsv', '', 256, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(34, 0, 0, 0, 15, 463, 0, 'Shop cart interface with steps', '', 'shop, user interface, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'AndrewEastwood', 'bootsnipp.com/AndrewEastwood', '', 317, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(35, 0, 0, 0, 1, 462, 0, 'No script alert', '', 'alert, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'kbjohnson90', 'twitter.com/kbjohnson90', '', 264, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(36, 0, 0, 0, 7, 436, 0, 'Bootstrap navigation with animation', '', 'navigation, animation, navigation with animation, boostrap navigation, navi, responsive navigation, responsive menu', 'maridlcrmn', 'twitter.com/maridlcrmn', '', 306, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(37, 0, 0, 0, 17, 437, 0, 'Block list with css blur effect', '', 'lists, blog, block, header, shopcode snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BhaumikPatel', 'twitter.com/patel0phone', '', 272, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(38, 0, 0, 0, 5, 356, 0, 'Search result list', '', 'search, lists, layouts, listing, item listing', 'gutomoraes', 'bootsnipp.com/gutomoraes', '', 310, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(39, 0, 0, 0, 17, 355, 0, 'Strapdown - Easy Markup for Bootstrap', '', 'jQuery plugin, layouts, markup', 'mrmccormack', 'bootsnipp.com/mrmccormack', '', 341, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(40, 0, 0, 0, 17, 353, 0, 'Contol panel menu, with navigation', '', 'menu, header, navigation, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'iosdsv', 'bootsnipp.com/iosdsv', '', 293, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(41, 0, 0, 0, 17, 351, 0, 'Sticky footer example', '', 'footer, layouts,  sticky footer, position fixed', 'mrmccormack', 'bootsnipp.com/mrmccormack', '', 278, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(42, 0, 0, 0, 17, 350, 0, 'Colorfull checkbox / radio boxes', '', 'checkbox, radio,  radio boxes, check boxes, bootstrap design', 'tonetlds', 'github.com/tonetlds', '', 213, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(43, 0, 0, 0, 11, 63, 0, 'Profile user interface', '', 'jQuery plugin, profile, user interface,  code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'msurguy', 'twitter.com/msurguy', '', 253, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(44, 0, 0, 0, 17, 59, 0, 'Animated square buttons', '', 'buttons,  square buttons', 'RowBootstrap', 'rowbootstrap.com', '', 203, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(45, 0, 0, 0, 6, 57, 0, 'Smallest login form', '', 'login, forms, login form', 'Mika', 'bootsnipp.com/Mika', '', 219, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(46, 0, 0, 0, 3, 19, 0, 'Bootstrap gallery thumbnails with social links', '', 'gallery, thumbnails, social thumbnails, galery thumb', 'mouse0270', 'twitter.com/mouse0270', '', 366, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(47, 0, 0, 0, 10, 610, 0, 'Bootstrap colored pricing table', '', 'colored, price list, price table, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 243, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(48, 0, 0, 0, 12, 248, 0, 'Product listing carousel', '', 'carousel, table, lists,  product list, listing with carousel', 'Cyruxx', 'github.com/Cyruxx', '', 315, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(49, 0, 0, 0, 17, 247, 0, 'Windows 8 style password reveal', '', 'user interface, forms, password form, form', 'stickerboy', 'bootsnipp.com/stickerboy', '', 223, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(50, 0, 0, 0, 17, 246, 0, 'Mobile-Friendly API Documentation', '', 'table, layouts, api documentation, document', 'travislaynewilson', 'twitter.com/travislayne', '', 259, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(51, 0, 0, 0, 17, 244, 0, 'Bootstrap multi level dropdown menu', '', 'menu, user, navbar,  multi level dropdown, dropdown', 'msurguy', 'twitter.com/msurguy', '', 271, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(52, 0, 0, 0, 17, 243, 0, 'Colored buttons with icons', '', 'buttons, user interface, colored buttons', 'BhaumikPatel', 'twitter.com/patel0phone', '', 233, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(53, 0, 0, 0, 17, 446, 0, 'Comment minimal form', '', 'lists, comment list, comment box', 'JGoodwillieV', 'twitter.com/jgoodwilliev', '', 255, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(54, 0, 0, 0, 17, 445, 0, 'Metro grid layout with icons', '', 'metro layout, metro icons, metro grid', 'joao12ferreira', 'twitter.com/joao12ferreira', '', 278, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(55, 0, 0, 0, 11, 412, 0, 'Default profile card', '', 'profile, user, lists', 'BhaumikPatel', 'twitter.com/patel0phone', '', 219, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(56, 0, 0, 0, 5, 416, 0, 'Comment listing with user profile thumb', '', 'user interface, lists, tabs, form, comment form', 'maridlcrmn', 'twitter.com/maridlcrmn', '', 280, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(57, 0, 0, 0, 11, 410, 0, 'Default profile card with social buttons', '', 'user interface, profile, responsive, user, social buttons, upser profile', 'maridlcrmn', 'twitter.com/maridlcrmn', '', 249, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(58, 0, 0, 0, 5, 409, 0, 'Crowdfunding thumbs', '', 'lists, user, table, thumb listing, crowdfounding listiing', 'msurguy', 'twitter.com/msurguy', '', 231, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(59, 0, 0, 0, 1, 272, 0, 'Colored large buttons', '', 'buttons, controls, admin dashboard', 'msurguy', 'twitter.com/msurguy', '', 313, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(60, 99, 0, 0, 6, 60, 0, 'Cool Login Box (HTML5 &amp; CSS3)', '', 'login box, login window, login page, login', 'Assad', 'facebook.com/sid.chaudhry.71', '', 382, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(61, 0, 0, 0, 7, 61, 0, 'Pure CSS Drop Down Menu', '', 'menu, navigation, responsive, animation', 'webdesignerhut', 'http://webdesignerhut.com/', '', 367, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(62, 0, 0, 0, 6, 237, 0, 'Bootstrap login form with background image', '', 'login, forms, user interface,, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BhaumikPatel', 'twitter.com/patel0phone', '', 438, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(63, 0, 0, 0, 11, 204, 0, 'User profile interface tab', '', 'user interface, user, profile, lists, tabs, user profile, profile', 'jessicarhawkins08', 'bootsnipp.com/jessicarhawkins08', '', 355, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(64, 0, 0, 0, 17, 203, 0, 'Bootstrap alert list', '', 'alert, lists,  alert, alert list, error', 'RodolfoSilva', 'bootsnipp.com/RodolfoSilva', '', 283, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(65, 0, 0, 0, 6, 202, 0, 'User login form', '', 'login, user, forms, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BhaumikPatel', 'twitter.com/patel0phone', '', 324, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(66, 0, 0, 0, 17, 201, 0, 'Company footer layout', '', 'footer, layouts, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'blairanderson', 'github.com/blairanderson', '', 245, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(67, 0, 0, 0, 17, 200, 0, 'Page 404 with bootstrap', '', 'page 404, layout, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'msurguy', 'twitter.com/msurguy', '', 315, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(68, 0, 0, 0, 5, 199, 0, 'Boxed list', '', 'lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Osom25', 'bootsnipp.com/Osom25', '', 292, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(69, 0, 0, 0, 17, 197, 0, 'Footer layout', '', 'footer, layouts, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'TXTCLASS', 'bootsnipp.com/TXTCLASS', '', 235, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(70, 0, 0, 0, 17, 206, 0, 'Dropdown buttons', '', 'menu, buttonsm code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'rodymol123', 'github.com/rodymol123', '', 278, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(71, 0, 0, 0, 15, 205, 0, 'Shop cart steps', '', 'lists, user interface,  shop cart, shop steps', 'brkrobert', 'bootsnipp.com/brkrobert', '', 283, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(72, 0, 0, 0, 2, 196, 0, 'Modal login / register box', '', 'signup, login, forms, modal, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'TXTCLASS', 'bootsnipp.com/TXTCLASS', '', 352, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(73, 0, 0, 0, 11, 174, 0, 'Rate label', '', 'user interface, progress bar,  code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BhaumikPatel', 'twitter.com/patel0phone', '', 204, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(74, 0, 0, 0, 5, 179, 0, 'Table / Fuzzy search example', '', 'jQuery plugin, search, lists, table, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'stidges', 'github.com/stidges', '', 323, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(75, 0, 0, 0, 3, 324, 0, 'Carousel product cart slider', '', 'cms, shop, ecommerce, carousel, cart slider, product slider', 'BhaumikPatel', 'twitter.com/patel0phone', '', 318, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(76, 0, 0, 0, 10, 770, 0, 'Bootstrap long pricing table', '', 'colored, price list, price table, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'DesignBootstrap', 'desisnbootstrap.com', '', 314, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(77, 0, 0, 0, 17, 769, 0, 'Bootstrap Subscribe Form', '', 'subscribe form, subscribe, form', 'DesignBootstrap', 'desisnbootstrap.com', '', 253, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(78, 0, 0, 0, 5, 768, 0, 'Bootstrap Order List', '', 'order list, admin, cms, table list', 'DesignBootstrap', 'desisnbootstrap.com', '', 419, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(79, 0, 0, 0, 17, 763, 0, 'Bootstrap features list  template', '', 'features list, icon list, icons, icon boxes, icon box', 'DesignBootstrap', 'desisnbootstrap.com', '', 343, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(80, 0, 0, 0, 17, 737, 0, 'Bootstrap Dashboard Boxes', '', 'admin, cms, boxes, admin boxes', 'DesignBootstrap', 'desisnbootstrap.com', '', 224, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(81, 0, 0, 0, 17, 785, 0, 'Bootstrap Notice Board', '', 'admin, cms, boxes, notice list', 'DesignBootstrap', 'desisnbootstrap.com', '', 847, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(82, 0, 0, 0, 5, 706, 0, 'Order history', '', 'snippets, bootstrap, css, html, lists, user interface, e-commerce', 'Jan Vorisek', 'snipplicious.com', '', 317, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(83, 0, 0, 0, 11, 707, 0, 'User profile', '', 'snippets, bootstrap, css, html, user interface, user profile, profile', 'Jan Vorisek', 'snipplicious.com', '', 334, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(84, 0, 0, 0, 4, 692, 0, 'Colored boxes', '', 'code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 296, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(85, 0, 0, 0, 6, 694, 0, 'User login', '', 'login box, login window, login page, login', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 260, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(86, 0, 0, 0, 3, 690, 0, 'Image Columns with bootstrap', '', 'images, thumbnails, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 308, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(87, 0, 0, 0, 12, 696, 0, 'Users products based on tabs', '', 'bootstrap,snippet,users,products,todo', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 347, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(88, 0, 0, 0, 3, 632, 0, 'Shopping cart', '', 'shopping, cart, list,products, user interface, lists, code snippets, bootstrap blocks, bootstrap, code blocks', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 304, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(89, 0, 0, 0, 7, 435, 0, 'Responsive side navigation menu with animation', '', 'menu, navigation, responsive, animation', 'joseanmola', 'twitter.com/joseanmola', '', 740, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(90, 0, 0, 0, 7, 433, 0, 'Navbar search', '', 'navbar, search', 'mouse0270', 'twitter.com/mouse0270', '', 298, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(91, 0, 0, 0, 3, 724, 0, 'Simple portfolio', '', 'snippets, bootstrap, css, html, images, user interface', 'Jan Vorisek', 'snipplicious.com', '', 339, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(92, 0, 0, 0, 11, 723, 0, 'Edit Profile Page', '', 'snippets, bootstrap, css, html, user interface, forms', 'Jan Vorisek', 'snipplicious.com', '', 328, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(93, 0, 0, 0, 9, 718, 0, 'Forum Posts', '', 'snippets, bootstrap, css, html, user interface', 'Jan Vorisek', 'snipplicious.com', '', 280, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(94, 0, 0, 0, 3, 717, 0, 'Creative portfolio with hover effect', '', 'snippets, bootstrap, css, html, images, effects', 'Jan Vorisek', 'snipplicious.com', '', 339, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(95, 0, 0, 0, 6, 716, 0, 'Trendy login form', '', 'snippets, bootstrap, css, html, forms, login form, login', 'Jan Vorisek', 'snipplicious.com', '', 281, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(96, 0, 0, 0, 10, 715, 0, 'Pricing tables', 'Simple pricing tables based on Bootstrap panels with few lines of CSS.', 'snippets, bootstrap, css, html, tables', 'Jan Vorisek', 'snipplicious.com', '', 312, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(97, 0, 0, 0, 5, 714, 0, 'Users list', 'User management with accordion to show details about users.', 'snippets, bootstrap, css, html, user interface', 'Jan Vorisek', 'snipplicious.com', '', 289, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(98, 0, 0, 0, 6, 726, 0, 'Simple login form', 'Simple sign in form using default Bootstrap classes and Font Awesome icons. This is a minimalist solution for any website.', 'snippets, bootstrap, css, html, forms, user interface', 'Jan Vorisek', 'snipplicious.com', '', 284, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(99, 0, 0, 0, 14, 712, 0, 'Colored Sign In Form', 'Colored login form made to impress! This amazing piece of design is a lot different from all the other sign in forms. Give it a try!', 'snippets, bootstrap, css, html, forms, user interface, effects', 'Jan Vorisek', 'snipplicious.com', '', 397, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(100, 0, 0, 0, 15, 711, 0, 'Another shopping cart', 'Summary in a shopping card. Usable for any ecommerce website using Bootstrap', 'snippets, bootstrap, css, html, e-commerce, lists, user interface', 'Jan Vorisek', 'snipplicious.com', '', 279, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(101, 0, 0, 0, 17, 101, 0, 'jQuery Accordion Demo', 'A simple content accordion built with jQuery', 'accordion, panels, accordion panels', 'webdesignerhut', 'http://webdesignerhut.com/', '', 319, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(102, 0, 0, 0, 10, 319, 0, 'Pricing table with feature', '', 'price table, shop,  table, responsive, price table, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'BhaumikPatel', 'twitter.com/patel0phone', '', 332, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(103, 0, 0, 0, 16, 317, 0, 'Responsive bootstrap carousel', '', 'carousel, layouts,  slider', 'BhaumikPatel', 'twitter.com/patel0phone', '', 372, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(104, 0, 0, 0, 17, 316, 0, 'Shop boxes with hover efect', '', 'shop, lists, panel, user interface,snippets, bootstrap, css, html', 'ErikAlserda', 'bootsnipp.com/ErikAlserda', '', 316, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(105, 0, 0, 0, 14, 318, 0, 'Registration form with terms', '', 'registration, forms, registration layout, registrationbox, registration window, registration page', 'MSBG', 'bootsnipp.com/MSBG', '', 343, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(106, 0, 0, 0, 17, 300, 0, 'Icons with text and animation effect', '', 'animation, icon boxes, image rounded, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'roxguel', 'bootsnipp.com/roxguel', '', 281, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(107, 0, 0, 0, 3, 260, 0, 'Bootstrap thumbnail gallery', '', 'gallery, thumbnails, image, image galery, thumbs,', 'mouse0270', 'twitter.com/mouse0270', '', 322, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(108, 0, 0, 0, 6, 261, 0, 'Login box with social buttons', '', 'login, social, social login, login buttons, login form', 'azhagu', 'bootsnipp.com/azhagu', '', 367, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(109, 0, 0, 0, 11, 704, 0, 'Bootstrap snippet Instagram user profile', '', 'user profile, profile header, profile', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 364, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(110, 0, 0, 0, 2, 110, 0, 'Cool modal dialog with animation effect', '', 'dialog, modal dialog, animation, transition', 'roundcubee', 'roundcubee.blogspot.com/', '', 425, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(111, 0, 0, 0, 6, 703, 0, 'Bootstrap site login page', '', 'login, site, page, home, login box, login window, login page, login', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 336, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(112, 0, 0, 0, 5, 701, 0, 'Home presentation layout with slider', '', 'carousel, layouts,  slider', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 299, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(113, 0, 0, 0, 6, 699, 0, 'Login box with social icons', '', 'login box, login window, login page, login', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 341, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(114, 0, 0, 0, 17, 691, 0, 'Dashboard user count colored circle boxes', '', 'colored, image,dashboard, boxes, circle, css, framework', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 376, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(115, 0, 0, 0, 5, 688, 0, 'Recent comments admin panel dashboard', '', 'comment list, recent comments, comments, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 350, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(116, 0, 0, 0, 1, 0, 0, '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(117, 0, 0, 0, 11, 695, 0, 'Social profile head', '', 'bootstrap,snippet,social ,profile, head, facebook, html, page, twitter, social network', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 311, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(118, 0, 0, 0, 6, 693, 0, 'Forgot password form', '', 'login box, login window, login page, login', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 313, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(119, 0, 0, 0, 11, 686, 0, 'Simple user profile description', '', 'user interface, profile, responsive, user, social buttons, user profile', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 279, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(120, 0, 0, 0, 17, 467, 0, 'Fancy bootstrap dropdowns', '', 'menu, buttons, navigation', 'mouse0270', 'twitter.com/mouse0270', '', 328, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(121, 0, 0, 0, 17, 468, 0, 'Tabs list with content', '', 'tabs, lists', 'keenthemes', 'keenthemes.com', '', 254, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(122, 0, 0, 0, 10, 460, 0, 'Pricing tables', '', 'table, ecommerce, shop, lists, colored, price list, price table, code snippets, bootstrap blocks,  code blocks, snippets', 'naeemchuhaan', 'bootsnipp.com/naeemchuhaan', '', 291, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(123, 0, 0, 0, 17, 454, 0, 'User interface, lists, panel, controls', '', 'user interface, lists, panel, controls', 'BhaumikPatel', 'twitter.com/patel0phone', '', 267, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(124, 0, 0, 0, 17, 447, 0, 'YouTube/Vimeo Responsive Embeds', '', 'videos, thumbnails, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'mrmccormack', 'bootsnipp.com/mrmccormack', '', 271, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(125, 0, 0, 0, 6, 451, 0, 'Login with social media icons', '', 'login box, login window, login page, login', 'blandman', 'bootsnipp.com/blandman', '', 340, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(126, 0, 0, 0, 6, 284, 0, 'Cloud login form', '', 'login box, login window, login page, login', 'Kaptenn', 'bootsnipp.com/Kaptenn', '', 388, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(127, 0, 0, 0, 1, 281, 0, 'Contact form with animation', '', 'contact, forms, animation', 'sergiopinnaprato', 'bootsnipp.com/sergiopinnaprato', '', 281, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(128, 0, 0, 0, 17, 276, 0, 'Mail box user interface', '', 'mailbox, layouts, user interface, lists', 'rgbskills', 'github.com/rgbskills', '', 332, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(129, 0, 0, 0, 17, 275, 0, 'Full admin dashboard', '', 'user interface, layouts,  admin dashboard, dashboard', 'bmoeller1', 'http://bootsnipp.com/bmoeller1', '', 300, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(130, 0, 0, 0, 3, 274, 0, 'Thumbnail gallery with dialog', '', 'modal, popup, images', 'Donny5300', 'github.com/Donny5300', '', 296, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(131, 0, 0, 0, 17, 273, 0, 'Colored alerts', '', 'alert, user interface', 'BhaumikPatel', 'twitter.com/patel0phone', '', 331, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(132, 0, 0, 0, 17, 271, 0, 'Timeline', '', 'user interface, lists, timeline, line, story', 'luisrudge', 'github.com/luisrudge', '', 318, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(133, 0, 0, 0, 17, 1, 0, 'Blockquote panels box for user interface', 'Panel blockquote boxes for user interface made with twitter bootstrap.', 'panel, user interface,  bootstrap,  html, css, js, code, snippet', 'BhaumikPatel', 'twitter.com/patel0phone', '', 317, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(134, 0, 0, 0, 17, 5, 0, 'Multiple inputs, checkboxes and textareas', 'Multiple inputs, checkboxes and textareas with maximum limit', 'multiple inputs, checkboxes, textareas, forms, jQuery plugin, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'zelenin', 'github.com/zelenin', '', 295, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(135, 0, 0, 0, 11, 11, 0, 'Shop cart checkout', 'Shop cart checkout', 'shop, cms, user interface, checkout, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'amatellanes', 'github.com/amatellanes', '', 327, 0, 0, 0, 2, 0, 0, 0, 1, '', '', ''),
(136, 0, 0, 0, 9, 10, 0, 'Event listing with day, image and social info', 'Event listing with day, image and social info, can be included evrywhere you want.', 'lists, responsive, user interface, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'mouse0270', 'twitter.com/mouse0270', '', 376, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(137, 0, 0, 0, 5, 9, 0, 'Table bootstrap grid', 'Table bassed on bootstrap', 'table, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'VimKanzo', 'http://bootsnipp.com/VimKanzo', '', 320, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(138, 0, 0, 0, 8, 8, 0, 'Bootstrap cardeostrap theme', 'Bootstrap cardeostrap theme with whole typhography', 'bootstrap, themes, theme, user interface, progres bars', 'msurguy', 'twitter.com/msurguy', '', 349, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(139, 0, 0, 0, 5, 453, 0, 'Table with add row', '', 'table, forms, table list, list', 'fractorr', 'http://bootsnipp.com/fractorr', '', 297, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(140, 0, 0, 0, 17, 448, 0, 'Social Buttons for Bootstrap', '', 'social, buttons, icon fonts', 'lipis', 'lip.is', '', 320, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(141, 0, 0, 0, 17, 651, 0, 'Bootstrap snippet contacts card', '', 'contacts,card,list,contact,people,image', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 343, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(142, 0, 0, 0, 11, 650, 0, 'User profile background', '', 'bootstrap, snippet, user, profile, options', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 371, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(143, 0, 0, 0, 17, 1122, 0, 'Modal dialog with animation effect', '', 'animation, icon boxes, image rounded, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 't4t5', 'github.com/t4t5', '', 401, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(158, 1, 0, 0, 1, 0, 0, 'Simple contact form on bootstrap', '', 'contact for', 'oncebuilder', 'snipplicious.com', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(160, 1, 0, 0, 17, 700, 0, 'Summary social networks', '', 'Bootstrap, Snippet, Html, Css, site,report,list,user,bootstrap,snippet,social,networks', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(146, 1, 0, 0, 10, 795, 0, 'Bootstrap pricing table example', '', '', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(144, 1, 0, 0, 17, 777, 0, 'Bootstrap Compose Message', '', '', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(159, 1, 0, 0, 6, 705, 0, 'Sign in + sign up form', '', 'snippets, bootstrap, css, html, forms, user interface', 'Jan Vorisek', 'snipplicious.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(147, 1, 0, 0, 15, 779, 0, 'Bootstrap Credit Card Form', '', 'shop cart, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(145, 1, 0, 0, 14, 781, 0, 'Bootstrap SignUp Form', '', 'signup, login, forms, modal, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(148, 1, 0, 0, 15, 767, 0, 'White Pricing Table', '', 'shopping, cart, list,products, user interface, lists, code snippets, bootstrap blocks, bootstrap, code blocks', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(149, 1, 0, 0, 17, 752, 0, 'Bootstrap Clients Reviews Example', '', 'code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(150, 1, 0, 0, 15, 738, 0, 'Bootstrap Cart Page', '', 'shopping, cart, list,products, user interface, lists, code snippets, bootstrap blocks, bootstrap, code blocks', 'DesignBootstrap', 'Designbootstrap.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(151, 1, 0, 0, 3, 727, 0, 'Event list', 'Simple and customisable event list suitable for any website that needs to list events or to attract people!', 'snippets, bootstrap, css, html, lists, effects', 'Jan Vorisek', 'snipplicious.com', '', 331, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(152, 1, 0, 0, 17, 725, 0, 'Minimal style comments', '', 'snippets, bootstrap, css, html, user interface', 'Jan Vorisek', 'snipplicious.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(153, 1, 0, 0, 3, 722, 0, 'Stylish skills listing', 'Rotating hover effect for modern portfolio.', 'snippets, bootstrap, css, html, user interface, effects', 'Jan Vorisek', 'snipplicious.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(154, 1, 0, 0, 14, 721, 0, 'Simply signup form', '', 'snippets, bootstrap, css, html, forms, user interface', 'Jan Vorisek', 'snipplicious.com', '', 338, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(155, 1, 0, 0, 17, 713, 0, 'FAQ with accordion', '', 'snippets, bootstrap, css, html, user interface, lists', 'Jan Vorisek', 'snipplicious.com', '', 341, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(156, 1, 0, 0, 17, 710, 0, 'Compose message', '', 'snippets, bootstrap, css, html, forms, user interface', 'Jan Vorisek', 'snipplicious.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(157, 1, 0, 0, 17, 709, 0, 'Forum', '', 'snippets, bootstrap, css, html, tables, user interface', 'Jan Vorisek', 'snipplicious.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(161, 1, 0, 0, 17, 698, 0, 'Social main articles', '', 'media,articles', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(162, 1, 0, 0, 17, 689, 0, 'Subscription form', '', 'subscription,form', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(163, 1, 0, 0, 4, 687, 0, 'Profile overview', '', 'bootstrap,snippet,profile,overview', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(164, 1, 0, 0, 4, 685, 0, 'Bootstrap snippet dismiss input field is part of the  g', 'Bootstrap snippet dismiss input field is part of the  gallery of free snippets for bootstrap css html js framework tags: input,field', 'Bootstrap, Snippet, Html, Css, input,field', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(165, 1, 0, 0, 17, 684, 0, '3 colors progress bar with', '', 'Bootstrap, Snippet, Html, Css, colors', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 298, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(166, 1, 0, 0, 4, 683, 0, 'Team member', '', 'Bootstrap, Snippet, Html, Css, users,list,team,bootstrap,snippet,content,social', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(167, 1, 0, 0, 17, 682, 0, 'Photo post', '', 'Bootstrap, Snippet, Html, Css, bootstrap, snippet, social, post', 'Deyson Bejarano', '', '', 290, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(168, 1, 0, 0, 17, 681, 0, 'User list inside narrow jumbotron is', '', 'Bootstrap, Snippet, Html, Css, list,user', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(169, 1, 0, 0, 17, 680, 0, 'Bootstrap snippet Full bootsra  3d Buttons is part of t', '', 'Bootstrap, Snippet, Html, Css,', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(170, 1, 0, 0, 17, 679, 0, 'Bootstrap snippet Highlighted contact form', '', 'Bootstrap, Snippet, Html, Css, form, contact,user', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(171, 1, 0, 0, 17, 678, 0, 'Ticket Board is part of the  gallery', '', 'Bootstrap, Snippet, Html, Css, bootstrap,snippet,ticket,board', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(172, 1, 0, 0, 17, 677, 0, 'Profile bio', '', '', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(173, 1, 0, 0, 17, 676, 0, 'Gmail inbox', '', 'Bootstrap, Snippet, Html, Css, mail,form, inbox, list', 'Deyson Bejarano', '', '', 316, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(174, 1, 0, 0, 17, 675, 0, 'Timeline with 10 Cool Bootstrap', '', 'Bootstrap, Snippet, Html, Css, timeline,button,switch,collapse,progress bar,progress', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(175, 1, 0, 0, 4, 674, 0, 'Rating Voting', '', 'Bootstrap, Snippet, Html, Css, voting,rating', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(176, 1, 0, 0, 4, 673, 0, 'Gmail style Sign in', '', 'Bootstrap, Snippet, Html, Css,', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(177, 1, 0, 0, 17, 672, 0, 'Support center', '', 'Bootstrap, Snippet, Html, Css, bootstrap,snippet,support,center,list', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 308, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(178, 1, 0, 0, 17, 671, 0, 'Select from dual list', '', 'Bootstrap, Snippet, Html, Css, list', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 320, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(179, 1, 0, 0, 11, 670, 0, 'Complete User Profile Page', '', 'Bootstrap, Snippet, Html, Css, page,complete', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(180, 1, 0, 0, 4, 667, 0, 'Cards panels with thumbnails', '', 'Bootstrap, Snippet, Html, Css, panel,cards,thumbnail', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(181, 1, 0, 0, 17, 666, 0, 'Portfolio with details on hover', '', 'Bootstrap, Snippet, Html, Css, list,detail,hover', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(182, 1, 0, 0, 11, 665, 0, 'Search Results with image', '', 'Bootstrap, Snippet, Html, Css, bootstrap,snippet,search,result,list', 'Deyson Bejarano', '', '', 333, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(183, 1, 0, 0, 17, 664, 0, 'Locked Screen', '', 'Bootstrap, Snippet, Html, Css, bootstrap,snippet,locked,screen', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(184, 1, 0, 0, 11, 663, 0, 'User profile resume', '', 'Bootstrap, Snippet, Html, Css, user,profile', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(185, 1, 0, 0, 6, 662, 0, 'Login form with icon', '', 'Bootstrap, Snippet, Html, Css, login,form,icon', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(186, 1, 0, 0, 17, 661, 0, 'Clients testimonial with small photo', '', 'Bootstrap, Snippet, Html, Css, testimonial,list,clients', 'Deyson Bejarano', 'bootdey.com/users/profile/Dey-Dey', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(187, 1, 0, 0, 7, 506, 0, 'Navigation menu', '', 'menu, navigation, icon fonts,  Bootstrap HTML CSS JS code snippet by DonSinDRom', 'DonSinDRom', 'github.com/DonSinDRom', '', 363, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(188, 1, 0, 0, 5, 92, 0, 'Admin user interface  lists', '', 'admin, user interface, lists,  Bootstrap HTML CSS JS code snippet by BhaumikPatel', 'BhaumikPatel', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(189, 1, 0, 0, 1, 91, 0, 'Bootstrap  Contact Form', '', 'cms, blog, layouts,  bootstrap', 'PawelK2012', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(190, 1, 0, 0, 6, 90, 0, 'Responsive login with social buttons', '', 'login, forms, responsive', 'DonSinDRom', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(191, 1, 0, 0, 10, 89, 0, 'Responsive price list', '', 'table, ecommerce, shop, lists, colored, price list, price table, code snippets, bootstrap blocks,  code blocks, snippets', 'HTMLAdmin.com', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(192, 1, 0, 0, 3, 88, 0, 'Flat User Profile / Interface UI', '', 'lists, user, profile', 'amite', '', '', 411, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(193, 1, 0, 0, 0, 87, 0, 'panel, animation, user interface,  Bootstrap snippet by', 'panel, animation, user interface,  Bootstrap HTML CSS JS code snippet by BhaumikPatel', 'panel, animation, user interface,  Bootstrap HTML CSS JS code snippet by BhaumikPatel', 'BhaumikPatel', 'twitter.com/patel0phone', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(194, 1, 0, 0, 1, 93, 0, 'Dashboard stats with transitions', '', 'social, admin dashboard, admin, user interface, lists', 'BhaumikPatel', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(195, 1, 0, 0, 0, 85, 0, 'forms, user interface,  Bootstrap snippet by maridlcrmn', 'forms, user interface,  Bootstrap HTML CSS JS code snippet by maridlcrmn', 'forms, user interface,  Bootstrap HTML CSS JS code snippet by maridlcrmn', 'maridlcrmn', 'twitter.com/maridlcrmn', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(196, 1, 0, 0, 11, 84, 0, 'Profile Card', '', 'user interface, profile, responsive, user, social buttons, user profile', 'shivkumarganesh', '', '', 462, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(197, 1, 0, 0, 7, 81, 0, 'Navigation responsive menu', '', 'menu, navigation, responsive, animation', 'BhaumikPatel', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(198, 1, 0, 0, 9, 78, 0, 'Product thumbnail listing for ecommerce', '', 'table, ecommerce, shop, lists, thumbnail, product list, product thumb, code snippets, bootstrap blocks', 'briandiaz', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(199, 1, 0, 0, 17, 75, 0, 'Form with char count', '', 'comment form, comment, from', 'msurguy', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(200, 1, 0, 0, 11, 659, 0, 'User profile canvas', '', 'profile, canvas, user, view user user interface, profile, responsive, user, social buttons, user profile', 'Deyson Bejarano', '', '', 458, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(201, 1, 0, 0, 17, 657, 0, 'Vs voting', '', 'rating, voting, social, favorite', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(202, 1, 0, 0, 5, 649, 0, 'Box funny with rate and description', '', 'bootstrap ,snippet, box, funny', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(203, 1, 0, 0, 17, 648, 0, 'About project', '', 'snippet, about, project', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(204, 1, 0, 0, 17, 644, 0, 'Facebook compose new post', '', 'code snippets, bootstrap blocks, bootstrap, code blocks', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(205, 1, 0, 0, 3, 643, 0, 'Nesting Columns', '', 'image, columns', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(206, 1, 0, 0, 17, 642, 0, 'Control Panel admin dashboard', '', 'social, admin dashboard, admin, user interface, lists', 'Deyson Bejarano', '', '', 482, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(207, 1, 0, 0, 17, 641, 0, 'Dashboard Statistics Overview', '', 'admin dashboard, admin, user interface, lists', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(208, 1, 0, 0, 17, 476, 0, 'Colorfull input groups', '', 'forms, controls, inputs, colored inputs', 'mouse0270', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(209, 1, 0, 0, 17, 472, 0, 'Accordion menu with icons', '', 'menu, controls, tabs, sidebar, navigation', 'BhaumikPatel', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(210, 1, 0, 0, 1, 215, 0, 'Contact form', '', 'contact, forms', 'BhaumikPatel', '', '', 443, 0, 0, 1, 1, 0, 0, 0, 1, '', '', ''),
(211, 1, 0, 0, 17, 211, 0, 'Timeline', 'timeline, lists,  Bootstrap HTML CSS JS code snippet by victoreduardo', 'timeline, lists,  Bootstrap HTML CSS JS code snippet by victoreduardo', 'victoreduardo', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(212, 1, 0, 0, 16, 210, 0, 'Carousel responsive header', '', 'layouts, slider, carousel, responsive, header,', 'maridlcrmn', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(213, 1, 0, 0, 17, 209, 0, 'Post new article admin panel', '', 'lists, forms', 'BhaumikPatel', '', '', 405, 0, 0, 1, 0, 0, 0, 0, 1, '', '', ''),
(214, 1, 0, 0, 3, 208, 0, 'Thumbnail on hover', '', 'thumbnails, images, gallery', 'Prasad', '', '', 411, 0, 0, 0, 1, 0, 0, 0, 1, '', '', ''),
(215, 1, 0, 0, 5, 198, 0, 'Table with live search', '', 'table, search, lists', 'Cyruxx', '', '', 467, 0, 0, 1, 2, 0, 0, 0, 1, '', '', ''),
(216, 1, 0, 0, 17, 2, 0, 'Advanced Dropdown Search From', '', 'search, forms, menu,  code snippets, bootstrap search, bootstrap, search form, snippet', 'iosdsv', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(217, 1, 0, 0, 17, 3, 0, 'Grid column re-ordering on mobile devices', '', 'grid column, responsive,  code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'mrmccormack', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(218, 1, 0, 0, 17, 6, 0, 'Modern 3D Flat Buttons', '', 'modern buttons, buttons, flat buttons, flat icons, icon fonts, buttons, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'nitroale', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(219, 1, 0, 0, 9, 7, 0, 'Blog post news', 'Blog post list can be used to show listing of your news or blog articles.', 'cms, user, lists, code snippets, bootstrap blocks, bootstrap, code blocks, snippets', 'msurguy', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(220, 1, 0, 0, 1, 13, 0, 'Nav Account Manager', '', 'navbar, navigation', 'wutlu', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(221, 1, 0, 0, 11, 653, 0, 'Profile history', '', 'snippet, profile, story', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(222, 1, 0, 0, 11, 653, 0, 'Profile history', '', 'snippet, profile, story', 'Deyson Bejarano', '', '', 516, 0, 0, 0, 0, 0, 0, 0, 1, '', '', ''),
(223, 1, 0, 0, 16, 652, 0, 'Carousel with face indicators', '', 'gallery, thumbnails, image, image galery, thumbs', '', '', '', 565, 0, 0, 1, 0, 0, 0, 0, 1, '', '', ''),
(224, 1, 0, 0, 17, 655, 0, 'Toggle Switch buttons', '', 'buttons, user interface, media buttons', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(225, 1, 0, 0, 4, 654, 0, 'Timeline with small images', '', 'Timeline,user,list,images', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(226, 1, 0, 0, 17, 639, 0, 'Profile overview and edit', '', 'bootstrap, snippet, profile, user, overview, edit profile, tabs, upload', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(227, 1, 0, 0, 17, 640, 0, 'zig zag user description', '', 'user, list', 'Deyson Bejarano', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(228, 106, 0, 0, 2, 228, 0, 'Usama Moin', '', 'Modal,Animated,Closeable', 'roundCube', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(229, 106, 0, 0, 2, 0, 0, 'Usama Moin', '', 'Modal, Animated, Closeable', 'roundCube', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(230, 107, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(231, 108, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(232, 1, 0, 0, 16, 0, 0, 'Featured carousel with round faces', '', 'gallery, thumbnails, image, image galery, thumbs', 'Deyson Bejarano', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(233, 114, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(234, 114, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(235, 116, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(236, 120, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(237, 125, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(238, 130, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(239, 132, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(240, 126, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(241, 135, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(242, 138, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(243, 148, 0, 0, 1, 0, 0, '', '', '', '', '', '', 2, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(244, 153, 0, 0, 3, 0, 0, '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(245, 154, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(246, 155, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(247, 155, 0, 0, 17, 247, 0, 'Object oriented programming', 'I generally try and describe Object-Orientated-Programming by using real world examples.\n\nFor example, I might say that a class called Vehicle describes the minimum things that a vehicle is. I\\''ll ask the person to tell me what he or she thinks a vehicle ', 'no one', 'Nazrana', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(248, 155, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(249, 157, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '');
INSERT INTO `edit_snippets` (`id`, `user_id`, `project_id`, `type_id`, `category_id`, `object_id`, `version`, `name`, `description`, `tags`, `author`, `author_url`, `licence`, `visits`, `comments`, `downloads`, `votes`, `reports`, `stared`, `created`, `updated`, `published`, `route`, `file`, `source`) VALUES
(250, 162, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(251, 173, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(252, 177, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(253, 184, 0, 0, 3, 0, 0, 'Thumbnail on hover', '', 'thumbnails, images, gallery', 'Prasad', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(254, 205, 0, 0, 1, 0, 0, '', '', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(255, 221, 0, 0, 1, 255, 0, 'test', 'test', 'test', 'indra', '', '', 2, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(256, 238, 0, 0, 0, 0, 0, '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(257, 239, 0, 0, 1, 0, 0, 'Contact form', '', 'contact, forms', 'BhaumikPatel', '', '', 2, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(258, 239, 0, 0, 5, 0, 0, 'Table with live search', 'Table search bassed on jQuery', 'table, search, lists', 'Cyruxx', '', '', 3, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(297, 1, 0, 0, 0, 297, 0, 'Once Contributors', 'Oncebuilder is a Open Source Project\nWe appreciate all contributors and creators of awesome libraries.', 'landing section', '', 'oncebuilder.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(296, 1, 0, 0, 0, 0, 0, 'Once Our Services Boxes', 'What do we know? What do we do?', 'our services section, icons, icon blocks, css blocks, css services', '', 'oncebuilder.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(295, 1, 0, 0, 0, 295, 0, 'Once About Us', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(294, 1, 0, 0, 4, 294, 0, 'Once Features More', 'Icon boxes with content and description', 'icons with description, icons code blocks, html blocks, css blocks, icos, descriptions, feature box', '', 'oncebuilder.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(299, 1, 0, 0, 0, 299, 0, 'Once Our Goals', 'Mission of oncebuilder is simply precised as it was at beginning.', 'our goals section, landing section', '', 'oncebuilder.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', ''),
(298, 1, 0, 0, 0, 0, 0, 'Once Request Button', 'Call to action request button', 'request button, landing section', '', 'oncebuilder.com', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets_categories`
--

CREATE TABLE `edit_snippets_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_snippets_categories`
--

INSERT INTO `edit_snippets_categories` (`id`, `project_id`, `name`, `ico`, `parent_id`, `level`, `position`) VALUES
(1, 1, 'Contact forms', 'fa fa-list-alt', 0, 0, 0),
(2, 1, 'Dialog pops', 'fa fa-rss-square', 0, 0, 0),
(3, 1, 'Galleries', 'fa fa-picture-o', 0, 0, 0),
(4, 1, 'Landing sections', 'fa fa-puzzle-piece', 0, 0, 0),
(5, 1, 'List tables', 'fa fa-table', 0, 0, 0),
(6, 1, 'Login forms', 'fa fa-sign-out', 0, 0, 0),
(7, 1, 'Navigations', 'fa fa-ellipsis-h', 0, 0, 0),
(8, 1, 'Newsletters', 'fa fa-envelope', 0, 0, 0),
(9, 1, 'Post listings', 'fa fa-th-list', 0, 0, 0),
(10, 1, 'Price tables', 'fa fa-columns', 0, 0, 0),
(11, 1, 'Profiles', 'fa fa-user', 0, 0, 0),
(12, 1, 'Products', 'fa fa-tags', 0, 0, 0),
(13, 1, 'Progress bars', 'fa fa-bars', 0, 0, 0),
(14, 1, 'Register forms', 'fa fa-sign-in', 0, 0, 0),
(15, 1, 'Shopping carts', 'fa fa-shopping-cart', 0, 0, 0),
(16, 1, 'Sliders', 'fa fa-caret-square-o-right', 0, 0, 0),
(17, 1, 'Uncategorized', 'fa fa-square', 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets_downloads`
--

CREATE TABLE `edit_snippets_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets_reports`
--

CREATE TABLE `edit_snippets_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets_visits`
--

CREATE TABLE `edit_snippets_visits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_snippets_votes`
--

CREATE TABLE `edit_snippets_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes`
--

CREATE TABLE `edit_themes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `category_tree` varchar(255) NOT NULL DEFAULT '',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `licence` varchar(255) NOT NULL DEFAULT '',
  `visits` int(11) NOT NULL DEFAULT '0',
  `price` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `reports` int(11) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0',
  `default` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_categories`
--

CREATE TABLE `edit_themes_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_themes_categories`
--

INSERT INTO `edit_themes_categories` (`id`, `project_id`, `name`, `ico`, `parent_id`, `level`, `position`) VALUES
(1, 1, 'Restaurants', 'fa fa-coffee', 0, 0, 0),
(2, 1, 'Internet Technology', 'fa fa-desktop', 0, 0, 0),
(4, 1, 'Hotels', 'fa fa-hotel', 0, 0, 0),
(5, 1, 'Building tech', 'fa fa-truck', 0, 0, 0),
(7, 1, 'Blogs', 'fa fa-feed', 0, 0, 0),
(9, 1, 'Landing pages', 'fa fa-spinner', 0, 0, 0),
(10, 1, 'Portfolios', 'fa fa-newspaper-o', 0, 0, 0),
(13, 1, 'House furnitures', 'fa fa-home', 0, 0, 0),
(14, 1, 'Sport', 'fa fa-soccer-ball-o', 0, 0, 0),
(16, 1, 'Finances', 'fa fa-bank', 0, 0, 0),
(17, 1, 'Games', 'fa fa-gamepad', 0, 0, 0),
(18, 1, 'Misc', 'fa fa-flask', 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_downloads`
--

CREATE TABLE `edit_themes_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `theme_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_langs`
--

CREATE TABLE `edit_themes_langs` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_reports`
--

CREATE TABLE `edit_themes_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `theme_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_visits`
--

CREATE TABLE `edit_themes_visits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `theme_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_themes_votes`
--

CREATE TABLE `edit_themes_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `theme_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tools`
--

CREATE TABLE `edit_tools` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `licence` varchar(255) NOT NULL DEFAULT '',
  `visits` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `reports` int(11) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0',
  `route` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials`
--

CREATE TABLE `edit_tutorials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL DEFAULT '0',
  `object_id` int(11) NOT NULL DEFAULT '0',
  `version` int(11) NOT NULL DEFAULT '0',
  `name` varchar(55) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `author` varchar(255) NOT NULL DEFAULT '',
  `author_url` varchar(255) NOT NULL DEFAULT '',
  `licence` varchar(255) NOT NULL DEFAULT '',
  `visits` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `votes` int(11) NOT NULL DEFAULT '0',
  `reports` int(11) NOT NULL DEFAULT '0',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `created` int(11) NOT NULL DEFAULT '0',
  `updated` int(11) NOT NULL DEFAULT '0',
  `published` int(11) NOT NULL DEFAULT '0',
  `route` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `source` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials_categories`
--

CREATE TABLE `edit_tutorials_categories` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `ico` varchar(255) NOT NULL DEFAULT '',
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials_downloads`
--

CREATE TABLE `edit_tutorials_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials_reports`
--

CREATE TABLE `edit_tutorials_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials_visits`
--

CREATE TABLE `edit_tutorials_visits` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_tutorials_votes`
--

CREATE TABLE `edit_tutorials_votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users`
--

CREATE TABLE `edit_users` (
  `id` int(11) NOT NULL,
  `login` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(16) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `api_key` varchar(32) NOT NULL DEFAULT '',
  `type_id` int(11) NOT NULL DEFAULT '-1',
  `referer_id` int(11) NOT NULL DEFAULT '0',
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `photo_url` varchar(255) NOT NULL DEFAULT '',
  `stared` tinyint(4) NOT NULL DEFAULT '0',
  `balance` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_activations`
--

CREATE TABLE `edit_users_activations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `user_ip` varchar(16) NOT NULL DEFAULT '',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `actived` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_aouth`
--

CREATE TABLE `edit_users_aouth` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `facebook` varchar(255) NOT NULL DEFAULT '',
  `twitter` varchar(255) NOT NULL DEFAULT '',
  `youtube` varchar(255) NOT NULL DEFAULT '',
  `linkedin` varchar(2000) NOT NULL DEFAULT '',
  `dribbble` varchar(100) NOT NULL DEFAULT '',
  `github` varchar(255) NOT NULL DEFAULT '',
  `google` varchar(100) NOT NULL DEFAULT '',
  `behance` varchar(100) NOT NULL DEFAULT '',
  `codepen` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_bans`
--

CREATE TABLE `edit_users_bans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `user_ip` varchar(16) NOT NULL DEFAULT '',
  `mktime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_counts`
--

CREATE TABLE `edit_users_counts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippets` int(11) NOT NULL DEFAULT '0',
  `plugins` int(11) NOT NULL DEFAULT '0',
  `themes` int(11) NOT NULL DEFAULT '0',
  `tutorials` int(11) NOT NULL DEFAULT '0',
  `followers` int(11) NOT NULL DEFAULT '0',
  `following` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_deletions`
--

CREATE TABLE `edit_users_deletions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `actived` int(11) NOT NULL DEFAULT '0',
  `reason` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_downloads`
--

CREATE TABLE `edit_users_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `snippet_id` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_follows`
--

CREATE TABLE `edit_users_follows` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `followed_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_informations`
--

CREATE TABLE `edit_users_informations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(254) NOT NULL DEFAULT '',
  `website` varchar(2000) NOT NULL DEFAULT '',
  `position` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(50) NOT NULL DEFAULT '',
  `company` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `address2` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(15) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `province` varchar(100) NOT NULL DEFAULT '',
  `country` varchar(100) NOT NULL DEFAULT '',
  `skills` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_logs`
--

CREATE TABLE `edit_users_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `type_id` tinyint(4) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `user_ip` varchar(16) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_messages`
--

CREATE TABLE `edit_users_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `user_to` int(11) NOT NULL DEFAULT '0',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `message` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_reminds`
--

CREATE TABLE `edit_users_reminds` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `mktime` int(11) NOT NULL DEFAULT '0',
  `actived` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_settings`
--

CREATE TABLE `edit_users_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `hire_form` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_socials`
--

CREATE TABLE `edit_users_socials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `facebook` varchar(50) NOT NULL DEFAULT '',
  `twitter` varchar(50) NOT NULL DEFAULT '',
  `youtube` varchar(254) NOT NULL DEFAULT '',
  `linkedin` varchar(2000) NOT NULL DEFAULT '',
  `dribbble` varchar(100) NOT NULL DEFAULT '',
  `github` varchar(255) NOT NULL DEFAULT '',
  `google` varchar(100) NOT NULL DEFAULT '',
  `behance` varchar(100) NOT NULL DEFAULT '',
  `codepen` varchar(100) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `edit_users_types`
--

CREATE TABLE `edit_users_types` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `position` int(11) NOT NULL DEFAULT '0',
  `perm` int(11) NOT NULL DEFAULT '0',
  `ico` varchar(55) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Zrzut danych tabeli `edit_users_types`
--

INSERT INTO `edit_users_types` (`id`, `project_id`, `name`, `position`, `perm`, `ico`, `action`) VALUES
(2, 1, 'Admins', 1, 1, 'fa fa-bug', ''),
(3, 1, 'Moderators', 2, 1, 'fa fa-edit', ''),
(4, 1, 'Advertisers', 4, 1, 'fa fa-bullhorn', ''),
(5, 1, 'Publishers', 6, 1, 'fa fa-video-camera', ''),
(6, 1, 'Reviewers', 5, 1, 'fa fa-thumbs-o-up', ''),
(1, 1, 'Creator', 0, 1, 'fa fa-keyboard-o', 'test');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `edit_documentation`
--
ALTER TABLE `edit_documentation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_faq`
--
ALTER TABLE `edit_faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_frameworks`
--
ALTER TABLE `edit_frameworks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_langs`
--
ALTER TABLE `edit_langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_langs_categories`
--
ALTER TABLE `edit_langs_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_langs_types`
--
ALTER TABLE `edit_langs_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `edit_layers`
--
ALTER TABLE `edit_layers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_layers_cols`
--
ALTER TABLE `edit_layers_cols`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_layers_rows`
--
ALTER TABLE `edit_layers_rows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_mailbox`
--
ALTER TABLE `edit_mailbox`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_mailbox_contacts`
--
ALTER TABLE `edit_mailbox_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_mailbox_types`
--
ALTER TABLE `edit_mailbox_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_menu`
--
ALTER TABLE `edit_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_pages`
--
ALTER TABLE `edit_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_pages_cols`
--
ALTER TABLE `edit_pages_cols`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_pages_rows`
--
ALTER TABLE `edit_pages_rows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_pages_types`
--
ALTER TABLE `edit_pages_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins`
--
ALTER TABLE `edit_plugins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins_categories`
--
ALTER TABLE `edit_plugins_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins_downloads`
--
ALTER TABLE `edit_plugins_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins_reports`
--
ALTER TABLE `edit_plugins_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins_visits`
--
ALTER TABLE `edit_plugins_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_plugins_votes`
--
ALTER TABLE `edit_plugins_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_posts`
--
ALTER TABLE `edit_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_posts_types`
--
ALTER TABLE `edit_posts_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_referers`
--
ALTER TABLE `edit_referers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_routes`
--
ALTER TABLE `edit_routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_routes_categories`
--
ALTER TABLE `edit_routes_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_settings`
--
ALTER TABLE `edit_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_settings_types`
--
ALTER TABLE `edit_settings_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets`
--
ALTER TABLE `edit_snippets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets_categories`
--
ALTER TABLE `edit_snippets_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets_downloads`
--
ALTER TABLE `edit_snippets_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets_reports`
--
ALTER TABLE `edit_snippets_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets_visits`
--
ALTER TABLE `edit_snippets_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_snippets_votes`
--
ALTER TABLE `edit_snippets_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes`
--
ALTER TABLE `edit_themes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_categories`
--
ALTER TABLE `edit_themes_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_downloads`
--
ALTER TABLE `edit_themes_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_langs`
--
ALTER TABLE `edit_themes_langs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_reports`
--
ALTER TABLE `edit_themes_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_visits`
--
ALTER TABLE `edit_themes_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_themes_votes`
--
ALTER TABLE `edit_themes_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tools`
--
ALTER TABLE `edit_tools`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials`
--
ALTER TABLE `edit_tutorials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials_categories`
--
ALTER TABLE `edit_tutorials_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials_downloads`
--
ALTER TABLE `edit_tutorials_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials_reports`
--
ALTER TABLE `edit_tutorials_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials_visits`
--
ALTER TABLE `edit_tutorials_visits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_tutorials_votes`
--
ALTER TABLE `edit_tutorials_votes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users`
--
ALTER TABLE `edit_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_activations`
--
ALTER TABLE `edit_users_activations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_aouth`
--
ALTER TABLE `edit_users_aouth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_bans`
--
ALTER TABLE `edit_users_bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_counts`
--
ALTER TABLE `edit_users_counts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_deletions`
--
ALTER TABLE `edit_users_deletions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_downloads`
--
ALTER TABLE `edit_users_downloads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_follows`
--
ALTER TABLE `edit_users_follows`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_informations`
--
ALTER TABLE `edit_users_informations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_logs`
--
ALTER TABLE `edit_users_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_messages`
--
ALTER TABLE `edit_users_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_reminds`
--
ALTER TABLE `edit_users_reminds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_settings`
--
ALTER TABLE `edit_users_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_socials`
--
ALTER TABLE `edit_users_socials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `edit_users_types`
--
ALTER TABLE `edit_users_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `edit_documentation`
--
ALTER TABLE `edit_documentation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- AUTO_INCREMENT dla tabeli `edit_faq`
--
ALTER TABLE `edit_faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT dla tabeli `edit_frameworks`
--
ALTER TABLE `edit_frameworks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT dla tabeli `edit_langs`
--
ALTER TABLE `edit_langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `edit_langs_categories`
--
ALTER TABLE `edit_langs_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_langs_types`
--
ALTER TABLE `edit_langs_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=185;
--
-- AUTO_INCREMENT dla tabeli `edit_layers`
--
ALTER TABLE `edit_layers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `edit_layers_cols`
--
ALTER TABLE `edit_layers_cols`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT dla tabeli `edit_layers_rows`
--
ALTER TABLE `edit_layers_rows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT dla tabeli `edit_mailbox`
--
ALTER TABLE `edit_mailbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT dla tabeli `edit_mailbox_contacts`
--
ALTER TABLE `edit_mailbox_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT dla tabeli `edit_mailbox_types`
--
ALTER TABLE `edit_mailbox_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `edit_menu`
--
ALTER TABLE `edit_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT dla tabeli `edit_pages`
--
ALTER TABLE `edit_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT dla tabeli `edit_pages_cols`
--
ALTER TABLE `edit_pages_cols`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT dla tabeli `edit_pages_rows`
--
ALTER TABLE `edit_pages_rows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `edit_pages_types`
--
ALTER TABLE `edit_pages_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins`
--
ALTER TABLE `edit_plugins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins_categories`
--
ALTER TABLE `edit_plugins_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins_downloads`
--
ALTER TABLE `edit_plugins_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins_reports`
--
ALTER TABLE `edit_plugins_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins_visits`
--
ALTER TABLE `edit_plugins_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52195;
--
-- AUTO_INCREMENT dla tabeli `edit_plugins_votes`
--
ALTER TABLE `edit_plugins_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `edit_posts`
--
ALTER TABLE `edit_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_posts_types`
--
ALTER TABLE `edit_posts_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT dla tabeli `edit_referers`
--
ALTER TABLE `edit_referers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `edit_routes`
--
ALTER TABLE `edit_routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;
--
-- AUTO_INCREMENT dla tabeli `edit_routes_categories`
--
ALTER TABLE `edit_routes_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_settings`
--
ALTER TABLE `edit_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_settings_types`
--
ALTER TABLE `edit_settings_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets`
--
ALTER TABLE `edit_snippets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=531;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets_categories`
--
ALTER TABLE `edit_snippets_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets_downloads`
--
ALTER TABLE `edit_snippets_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets_reports`
--
ALTER TABLE `edit_snippets_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets_visits`
--
ALTER TABLE `edit_snippets_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52236;
--
-- AUTO_INCREMENT dla tabeli `edit_snippets_votes`
--
ALTER TABLE `edit_snippets_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT dla tabeli `edit_themes`
--
ALTER TABLE `edit_themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_categories`
--
ALTER TABLE `edit_themes_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_downloads`
--
ALTER TABLE `edit_themes_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_langs`
--
ALTER TABLE `edit_themes_langs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_reports`
--
ALTER TABLE `edit_themes_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_visits`
--
ALTER TABLE `edit_themes_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT dla tabeli `edit_themes_votes`
--
ALTER TABLE `edit_themes_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT dla tabeli `edit_tools`
--
ALTER TABLE `edit_tools`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials`
--
ALTER TABLE `edit_tutorials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials_categories`
--
ALTER TABLE `edit_tutorials_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials_downloads`
--
ALTER TABLE `edit_tutorials_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials_reports`
--
ALTER TABLE `edit_tutorials_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials_visits`
--
ALTER TABLE `edit_tutorials_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_tutorials_votes`
--
ALTER TABLE `edit_tutorials_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users`
--
ALTER TABLE `edit_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=584;
--
-- AUTO_INCREMENT dla tabeli `edit_users_activations`
--
ALTER TABLE `edit_users_activations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=231;
--
-- AUTO_INCREMENT dla tabeli `edit_users_aouth`
--
ALTER TABLE `edit_users_aouth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users_bans`
--
ALTER TABLE `edit_users_bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users_counts`
--
ALTER TABLE `edit_users_counts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_users_deletions`
--
ALTER TABLE `edit_users_deletions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_users_downloads`
--
ALTER TABLE `edit_users_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT dla tabeli `edit_users_follows`
--
ALTER TABLE `edit_users_follows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users_informations`
--
ALTER TABLE `edit_users_informations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
--
-- AUTO_INCREMENT dla tabeli `edit_users_logs`
--
ALTER TABLE `edit_users_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users_messages`
--
ALTER TABLE `edit_users_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT dla tabeli `edit_users_reminds`
--
ALTER TABLE `edit_users_reminds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
--
-- AUTO_INCREMENT dla tabeli `edit_users_settings`
--
ALTER TABLE `edit_users_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT dla tabeli `edit_users_socials`
--
ALTER TABLE `edit_users_socials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT dla tabeli `edit_users_types`
--
ALTER TABLE `edit_users_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
