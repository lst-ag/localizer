<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "localizer"
 *
 * Auto generated by Extension Builder 2015-07-13
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title'            => 'Localizer for TYPO3',
    'description'      => 'This extension provides a fully automated workflow and a graphical user interface for the well known Localization Manager (l10nmgr). While the L10nmgr still provides exports and imports of records and files, the Localizer will take care of all the necessary steps in between. Editors responsible for translations won\'t have to deal with any L10nmgr configurations anymore and as an administrator you create just one configuration per Localizer Project.',
    'category'         => 'module',
    'author'           => 'Jo Hasenau, Peter Russ',
    'author_email'     => 'jh@cybercraft.de, peter.russ@4many.net',
    'state'            => 'stable',
    'internal'         => '',
    'uploadfolder'     => '0',
    'createDirs'       => '',
    'clearCacheOnLoad' => 0,
    'version'          => '8.2.0',
    'constraints'      => [
        'depends'   => [
            'typo3'              => '8.7.0-8.7.99',
            'scheduler'          => '8.7.0-8.7.99',
            'static_info_tables' => '6.2.0-0.0.0',
            'l10nmgr'            => '8.6.0-0.0.0',
        ],
        'conflicts' => [],
        'suggests'  => [],
    ],
];