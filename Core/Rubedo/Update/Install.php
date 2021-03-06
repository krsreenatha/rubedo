<?php
/**
 * Rubedo -- ECM solution
 * Copyright (c) 2013, WebTales (http://www.webtales.fr/).
 * All rights reserved.
 * licensing@webtales.fr
 *
 * Open Source License
 * ------------------------------------------------------------------------------------------
 * Rubedo is licensed under the terms of the Open Source GPL 3.0 license.
 *
 * @category   Rubedo
 * @package    Rubedo
 * @copyright  Copyright (c) 2012-2013 WebTales (http://www.webtales.fr)
 * @license    http://www.gnu.org/licenses/gpl.html Open Source GPL 3.0 license
 */
namespace Rubedo\Update;

use Rubedo\Services\Manager;
use WebTales\MongoFilters\Filter;
use Rubedo;

/**
 * Methods
 * for
 * install
 * tool
 *
 * @author
 *         jbourdin
 *        
 */
class Install
{

    protected static $translateService;

    public static function doInsertContents()
    {
        $defaultLocale = Manager::getService('Languages')->getDefaultLanguage();
        if (! $defaultLocale) {
            return false;
        }
        \Rubedo\Internationalization\Translate::setDefaultLanguage($defaultLocale);
        
        $translateService = Manager::getService('Translate');
        
        $success = true;
        
        $contentPath = APPLICATION_PATH . '/../data/default/';
        $contentIterator = new \DirectoryIterator($contentPath);
        foreach ($contentIterator as $directory) {
            if ($directory->isDot() || ! $directory->isDir()) {
                continue;
            }
            if (in_array($directory->getFilename(), array(
                'groups',
                'site'
            ))) {
                continue;
            }
            $collection = ucfirst($directory->getFilename());
            $collectionService = Manager::getService($collection);
            $isLocalizable = $collectionService instanceof Rubedo\Collection\AbstractLocalizableCollection;
            $itemsJson = new \DirectoryIterator($contentPath . '/' . $directory->getFilename());
            foreach ($itemsJson as $file) {
                if ($file->isDot() || $file->isDir()) {
                    continue;
                }
                if ($file->getExtension() == 'json') {
                    $itemJson = file_get_contents($file->getPathname());
                    
                    $itemJson = preg_replace_callback('/###(.*)###/U', array(
                        'Rubedo\\Update\\Install',
                        'replaceWithTranslation'
                        ), $itemJson);
                    
                    $item = \Zend_Json::decode($itemJson);
                    
                    
                    try {
                        if (! $collectionService->findOne(Filter::factory('Value')->setName('defaultId')
                            ->setValue($item['defaultId']))) {
                            $result = $collectionService->create($item);
                        } else {
                            $result['success'] = true;
                        }
                    } catch (\Rubedo\Exceptions\User $exception) {
                        $result['success'] = true;
                    }
                    
                    $success = $result['success'] && $success;
                }
            }
        }
        return $success;
    }

    public static function replaceWithTranslation($matches)
    {
        if (is_null(self::$translateService)) {
            $defaultLocale = Manager::getService('Languages')->getDefaultLanguage();
            \Rubedo\Internationalization\Translate::setDefaultLanguage($defaultLocale);
            
            self::$translateService = Manager::getService('Translate');
        }
        if($matches[1]=='Locale'){
            return \Rubedo\Internationalization\Translate::getDefaultLanguage();
        }
        $result = self::$translateService->translate($matches[1]);
        if(empty($result)){
            throw new \Rubedo\Exceptions\Server('can\'t translate :'.$matches[1]);
        }
        return $result;
    }

    public static function doCreateDefaultsGroups()
    {
        $defaultLocale = Manager::getService('Languages')->getDefaultLanguage();
        if (! $defaultLocale) {
            return false;
        }
        \Rubedo\Internationalization\Translate::setDefaultLanguage($defaultLocale);
        
        try {
            $adminWorkspaceId = Manager::getService('Workspaces')->getAdminWorkspaceId();
            if (! $adminWorkspaceId) {
                Manager::getService('Workspaces')->create(array(
                    'text' => Manager::getService('Translate')->translate("Workspace.admin", 'admin'),
                    'nativeLanguage' => $defaultLocale
                ));
            }
        } catch (Rubedo\Exceptions\User $exception) {
            // dont
            // stop
            // if
            // already
            // exists
        }
        $adminWorkspaceId = Manager::getService('Workspaces')->getAdminWorkspaceId();
        
        $success = true;
        $groupsJsonPath = APPLICATION_PATH . '/../data/default/groups';
        $groupsJson = new \DirectoryIterator($groupsJsonPath);
        foreach ($groupsJson as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            if ($file->getExtension() == 'json') {
                $itemJson = file_get_contents($file->getPathname());
                
                $itemJson = preg_replace_callback('/###(.*)###/U', array(
                    'Rubedo\\Update\\Install',
                    'replaceWithTranslation'
                ), $itemJson);
                
                $item = \Zend_Json::decode($itemJson);
                                
                if ($item['name'] == 'admin') {
                    $item['workspace'] = $adminWorkspaceId;
                    $item['inheritWorkspace'] = false;
                }
                $result = Manager::getService('Groups')->create($item);
                $success = $result['success'] && $success;
            }
        }
        
        return $success;
    }

    public static function setDbVersion($version)
    {
        Manager::getService('RubedoVersion')->setDbVersion($version);
    }

    /**
     * Import
     * in
     * languages
     * collection
     * all
     * languages
     * form
     * iso-639
     */
    public static function importLanguages()
    {
        $tsvFile = APPLICATION_PATH . '/../data/ISO-639-2_utf-8.txt';
        $file = fopen($tsvFile, 'r');
        $service = Manager::getService('Languages');
        while ($line = fgetcsv($file, null, '|')) {
            if (empty($line[2])) {
                continue;
            }
            $lang = array();
            $lang['iso2'] = $line[2];
            $lang['locale'] = $line[2];
            $lang['iso3'] = $line[0];
            $lang['label'] = $line[3];
            $lang['labelFr'] = $line[4];
            
            $upsertFilter = Filter::factory('Value')->setName('locale')->setValue($lang['locale']);
            $service->create($lang, array(
                'upsert' => $upsertFilter
            ));
        }
        return true;
    }

    /**
     * Set
     * a
     * language
     * as
     * default
     * language
     *
     * @param string $locale            
     * @return boolean
     */
    public static function setDefaultRubedoLanguage($locale)
    {
        $service = Manager::getService('Languages');
        
        $options = array(
            'multiple' => true
        );
        
        // ensure
        // only
        // one
        // default
        // exist
        $data = array(
            '$set' => array(
                'isDefault' => false
            )
        );
        $service->customUpdate($data, Filter::factory(), $options);
        
        // set
        // selected
        // language
        // to
        // active
        // and
        // default
        $data = array(
            '$set' => array(
                'isDefault' => true,
                'active' => true
            )
        );
        $filter = Filter::factory('Value')->setName('locale')->setValue($locale);
        $service->customUpdate($data, $filter, $options);
        
        // set
        // default
        // language
        // for
        // existing
        // sites
        $data = array(
            '$set' => array(
                'locStrategy' => 'onlyOne',
                'defaultLanguage' => $locale,
                'languages' => array(
                    $locale
                )
            )
        );
        $updateCond = Filter::factory('OperatorToValue')->setName('locStrategy')
            ->setOperator('$exists')
            ->setValue(false);
        $options = array(
            'multiple' => true
        );
        Manager::getService('Sites')->customUpdate($data, $updateCond, $options);
        
        // set
        // default
        // working
        // language
        // for
        // BO
        // for
        // users
        $data = array(
            '$set' => array(
                'workingLanguage' => $locale
            )
        );
        $updateCond = Filter::factory('OperatorToValue')->setName('workingLanguage')
            ->setOperator('$exists')
            ->setValue(false);
        $options = array(
            'multiple' => true
        );
        Manager::getService('Users')->customUpdate($data, $updateCond, $options);
        
        // ensure
        // that
        // localizable
        // collections
        // are
        // now
        // localized
        \Rubedo\Collection\AbstractLocalizableCollection::localizeAllCollection();
        
        return true;
    }
}