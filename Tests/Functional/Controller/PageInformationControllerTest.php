<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Info\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Info\Controller\PageInformationController;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class PageInformationControllerTest extends FunctionalTestCase
{
    protected function setUp(): void
    {
        $this->coreExtensionsToLoad[] = 'info';
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/DataSets/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../Fixtures/DataSets/pages.csv');
        $this->setUpBackendUser(1);
    }

    public static function protectedGetPageRecordsRecursiveDataSets(): \Generator
    {
        yield 'first level with one depth level' => [
            'pid' => 1,
            'depth' => 1,
            'expectedRows' => [
                0 => [
                    'uid' => 2,
                    'pid' => 1,
                    'tstamp' => 0,
                    'crdate' => 0,
                    'cruser_id' => 0,
                    'deleted' => 0,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '0',
                    'sorting' => 2,
                    'rowDescription' => null,
                    'editlock' => 0,
                    'sys_language_uid' => 0,
                    'l10n_parent' => 0,
                    'l10n_source' => 0,
                    'l10n_state' => null,
                    't3_origuid' => 0,
                    'l10n_diffsource' => '',
                    't3ver_oid' => 0,
                    't3ver_wsid' => 0,
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    'perms_userid' => 0,
                    'perms_groupid' => 0,
                    'perms_user' => 0,
                    'perms_group' => 0,
                    'perms_everybody' => 0,
                    'title' => 'site-root/page-1',
                    'slug' => '',
                    'doktype' => 1,
                    'TSconfig' => null,
                    'php_tree_stop' => 0,
                    'categories' => 0,
                    'layout' => 0,
                    'extendToSubpages' => 0,
                    'nav_title' => '',
                    'nav_hide' => 0,
                    'subtitle' => '',
                    'target' => '',
                    'url' => '',
                    'lastUpdated' => 0,
                    'newUntil' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_search' => 0,
                    'shortcut_mode' => 0,
                    'keywords' => null,
                    'description' => null,
                    'abstract' => null,
                    'author' => '',
                    'author_email' => '',
                    'media' => 0,
                    'is_siteroot' => 0,
                    'mount_pid_ol' => 0,
                    'module' => '',
                    'l18n_cfg' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'tsconfig_includes' => '',
                    'treeIcons' => '<span class="treeline-icon treeline-icon-joinbottom"></span>',
                ],
            ],
        ];
        yield 'first level with depth of 4 levels' => [
            'pid' => 1,
            'depth' => 4,
            'expectedRows' => [
                0 => [
                    'uid' => 2,
                    'pid' => 1,
                    'tstamp' => 0,
                    'crdate' => 0,
                    'cruser_id' => 0,
                    'deleted' => 0,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '0',
                    'sorting' => 2,
                    'rowDescription' => null,
                    'editlock' => 0,
                    'sys_language_uid' => 0,
                    'l10n_parent' => 0,
                    'l10n_source' => 0,
                    'l10n_state' => null,
                    'l10n_diffsource' => '',
                    't3ver_oid' => 0,
                    't3ver_wsid' => 0,
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    'perms_userid' => 0,
                    'perms_groupid' => 0,
                    'perms_user' => 0,
                    'perms_group' => 0,
                    'perms_everybody' => 0,
                    'SYS_LASTCHANGED' => 0,
                    'shortcut' => 0,
                    'content_from_pid' => 0,
                    'mount_pid' => 0,
                    'doktype' => 1,
                    'title' => 'site-root/page-1',
                    'slug' => '',
                    'TSconfig' => null,
                    'php_tree_stop' => 0,
                    'categories' => 0,
                    'layout' => 0,
                    'extendToSubpages' => 0,
                    'nav_title' => '',
                    'nav_hide' => 0,
                    'subtitle' => '',
                    'target' => '',
                    'url' => '',
                    'lastUpdated' => 0,
                    'newUntil' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_search' => 0,
                    'shortcut_mode' => 0,
                    'keywords' => null,
                    'description' => null,
                    'abstract' => null,
                    'author' => '',
                    'author_email' => '',
                    'media' => 0,
                    'is_siteroot' => 0,
                    'mount_pid_ol' => 0,
                    'module' => '',
                    'l18n_cfg' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'tsconfig_includes' => '',
                    'treeIcons' => '<span class="treeline-icon treeline-icon-joinbottom"></span>',
                ],
                1 => [
                    'uid' => 4,
                    'pid' => 2,
                    'tstamp' => 0,
                    'crdate' => 0,
                    'cruser_id' => 0,
                    'deleted' => 0,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '0',
                    'sorting' => 2,
                    'rowDescription' => null,
                    'editlock' => 0,
                    'sys_language_uid' => 0,
                    'l10n_parent' => 0,
                    'l10n_source' => 0,
                    'l10n_state' => null,
                    'l10n_diffsource' => '',
                    't3ver_oid' => 0,
                    't3ver_wsid' => 0,
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    'perms_userid' => 0,
                    'perms_groupid' => 0,
                    'perms_user' => 0,
                    'perms_group' => 0,
                    'perms_everybody' => 0,
                    'SYS_LASTCHANGED' => 0,
                    'shortcut' => 0,
                    'content_from_pid' => 0,
                    'mount_pid' => 0,
                    'doktype' => 1,
                    'title' => 'site-root/page-1/page-1',
                    'slug' => '',
                    'TSconfig' => null,
                    'php_tree_stop' => 0,
                    'categories' => 0,
                    'layout' => 0,
                    'extendToSubpages' => 0,
                    'nav_title' => '',
                    'nav_hide' => 0,
                    'subtitle' => '',
                    'target' => '',
                    'url' => '',
                    'lastUpdated' => 0,
                    'newUntil' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_search' => 0,
                    'shortcut_mode' => 0,
                    'keywords' => null,
                    'description' => null,
                    'abstract' => null,
                    'author' => '',
                    'author_email' => '',
                    'media' => 0,
                    'is_siteroot' => 0,
                    'mount_pid_ol' => 0,
                    'module' => '',
                    'l18n_cfg' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'tsconfig_includes' => '',
                    'treeIcons' => '<span class="treeline-icon treeline-icon-clear"></span><span class="treeline-icon treeline-icon-join"></span>',
                ],
                2 => [
                    'uid' => 3,
                    'pid' => 2,
                    'tstamp' => 0,
                    'crdate' => 0,
                    'cruser_id' => 0,
                    'deleted' => 0,
                    'hidden' => 0,
                    'starttime' => 0,
                    'endtime' => 0,
                    'fe_group' => '0',
                    'sorting' => 4,
                    'rowDescription' => null,
                    'editlock' => 0,
                    'sys_language_uid' => 0,
                    'l10n_parent' => 0,
                    'l10n_source' => 0,
                    'l10n_state' => null,
                    'l10n_diffsource' => '',
                    't3ver_oid' => 0,
                    't3ver_wsid' => 0,
                    't3ver_state' => 0,
                    't3ver_stage' => 0,
                    'perms_userid' => 0,
                    'perms_groupid' => 0,
                    'perms_user' => 0,
                    'perms_group' => 0,
                    'perms_everybody' => 0,
                    'SYS_LASTCHANGED' => 0,
                    'shortcut' => 0,
                    'content_from_pid' => 0,
                    'mount_pid' => 0,
                    'doktype' => 1,
                    'title' => 'site-root/page-1/page-2',
                    'slug' => '',
                    'TSconfig' => null,
                    'php_tree_stop' => 0,
                    'categories' => 0,
                    'layout' => 0,
                    'extendToSubpages' => 0,
                    'nav_title' => '',
                    'nav_hide' => 0,
                    'subtitle' => '',
                    'target' => '',
                    'url' => '',
                    'lastUpdated' => 0,
                    'newUntil' => 0,
                    'cache_timeout' => 0,
                    'cache_tags' => '',
                    'no_search' => 0,
                    'shortcut_mode' => 0,
                    'keywords' => null,
                    'description' => null,
                    'abstract' => null,
                    'author' => '',
                    'author_email' => '',
                    'media' => 0,
                    'is_siteroot' => 0,
                    'mount_pid_ol' => 0,
                    'module' => '',
                    'l18n_cfg' => 0,
                    'backend_layout' => '',
                    'backend_layout_next_level' => '',
                    'tsconfig_includes' => '',
                    'treeIcons' => '<span class="treeline-icon treeline-icon-clear"></span><span class="treeline-icon treeline-icon-joinbottom"></span>',
                ],
            ],
        ];
    }

    /**
     * @dataProvider protectedGetPageRecordsRecursiveDataSets
     * @test
     */
    public function protectedGetPageRecordsRecursiveReturnsExpectedResult(
        int $pid,
        int $depth,
        array $expectedRows
    ): void {
        $expectedRows = $this->prepareArray($expectedRows);
        $subject = $this->get(PageInformationController::class);
        $getPageRecordsRecursive = new \ReflectionMethod($subject, 'getPageRecordsRecursive');
        $getPageRecordsRecursive->setAccessible(true);
        self::assertSame($expectedRows, $this->prepareArray($getPageRecordsRecursive->invoke($subject, $pid, $depth)));
    }

    private function prepareArray(array $array): array
    {
        foreach ($array as $key => &$value) {
            $value = ksort($value);
        }
        return $array;
    }
}
