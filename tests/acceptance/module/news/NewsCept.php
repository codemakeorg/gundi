<?php

use \Module\News\Model\Category as CategoryModel;
use \Module\News\Model\News as NewsModel;


CategoryModel::truncate();
NewsModel::truncate();

$oCategory = CategoryModel::create(['name' => 'test']);

$I = new AcceptanceTester($scenario);

$I->maximizeWindow();
$I->amOnPage('/#/news');
$I->waitForText('News', null, '.page-header');


/**
 * *********************************
 * Testing add
 * *********************************
 */
$I->click('#news_add');
$I->waitForText('Add News');
$I->click('button[type="submit"]');
$I->waitForText('Select category');
$I->fillField('#title', 'test');
$I->selectOption('category', $oCategory->getKey());
$I->executeJS("CKEDITOR.instances['ckeditor'].setData('".'text'."');");
$I->click('button[type="submit"]');
$I->waitForText('News successfully added');
$I->see('test');
$I->seeElement('button[title="Publish"]');
//test filter
$I->fillField('#filter-title', 'te');
$I->click('.filter-btn');
$I->waitForText('test');

/**
 * *******************************
 * Testing update
 * *******************************
 */
$I->click('.news-edit-btn');
$I->fillField('title', 'test2');
$I->checkOption('#published');
$I->selectOption('goto', 'back');
$I->click('button[type="submit"]');
$I->waitForText('News successfully saved');
$I->see('test2');
$I->seeElement('button[title="Hide"]');
$I->seeElement('button[title="View"]');

/**
 * ********************************
 * Testing Hide
 * ********************************
 */
$I->click('button[title="Hide"]');
$I->waitForText('News successfully unpublished');
$I->seeElement('button[title="Publish"]');
$I->dontSeeElement('button[title="View"]');
$I->dontSeeElement('button[title="Hide"]');
/**
 * *******************************
 * Testing Publish
 * *******************************
 */
$I->click('button[title="Publish"]');
$I->waitForText('News successfully unpublished');
$I->dontSeeElement('button[title="Publish"]');
$I->seeElement('button[title="Hide"]');
$I->seeElement('button[title="View"]');
/**
 * ********************************
 * Testing View
 * ********************************
 */
$I->click('button[title="View"]');
$I->waitForText('test2', null, '.page-header');
$I->see('text');

/**
 * *******************************
 * Testing delete
 * *******************************
 */
$I->amOnPage('/#/news');
$I->waitForText('News', null, '.page-header');
$I->click('.btn-danger');
$I->waitForText('News successfully deleted');
