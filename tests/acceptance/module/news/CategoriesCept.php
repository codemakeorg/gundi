<?php
use \Module\News\Model\Category as CategoryModel;

CategoryModel::truncate();

$mCategories[] = CategoryModel::create(
    [
        'name' => 'cat1',
        'description' => 'desc1'
    ]
);
$mCategories[] = CategoryModel::create(
    [
        'name' => 'cat2',
        'description' => 'desc2'
    ]
);

$I = new AcceptanceTester($scenario);

$I->maximizeWindow();
$I->amOnPage('/#/categories');
$I->waitForText('Categories', null, '.page-header');

/**
 * *******************************
 * testing add
 * *******************************
 */

$I->click('#category_new');
$I->waitForText('Add Category');
$I->fillField('#name', 'test');
$I->executeJS("CKEDITOR.instances['ckeditor'].setData('".'desc'."');");
$I->click('button[type="submit"]');
$I->waitForText('Category successfully added');
$I->see('test');

/**
 * *******************************
 * testing update
 * *******************************
 */
$I->click('.category-edit');
$I->waitForText('Edit Category');
$I->fillField('#name', 'updated');
$I->click('button[type="submit"]');
$I->waitForText('Category successfully saved');
$I->amOnPage('/#/categories');
$I->waitForText('Categories', null, '.page-header');
$I->see('updated');

/**
 * *******************************
 * testing delete
 * *******************************
 */
$I->click('.btn-danger');
$I->waitForText('Category successfully deleted');
