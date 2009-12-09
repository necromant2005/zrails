<?php
require_once 'Zrails/Db/TableInit.php';

class Zrails_Db_TableTest extends Zrails_Db_TableInit
{
    public function testTableAdapter()
    {
        $this->assertEquals(get_class($this->users->getAdapter()), 'Zend_Db_Adapter_Pdo_Mysql');
    }

    public function testFindRow()
    {
        $user = $this->users->findRow(1);
        $this->assertEquals(get_class($user), 'Test_Model_User');
        $this->assertEquals($user->id, 1);

        $user = $this->users->findRow(100);
        $this->assertNull($user);
    }

    public function testFindOrCreate()
    {
        $user = $this->users->findOrCreateRow(1);
        $this->assertEquals(get_class($user), 'Test_Model_User');
        $this->assertEquals($user->id, 1);
        $this->assertTrue($user->isValid(), 1);

        $user = $this->users->findOrCreateRow(100);
        $this->assertEquals(get_class($user), 'Test_Model_User');
        $this->assertEquals($user->getId(), null);
        $this->assertTrue($user->isNotValid());
        $this->assertFalse($user->isValid());
    }

    public function testFetchAllPages()
    {
        $users = $this->users->fetchAllPages(1, 'id ASC', 1, 1);
        $this->assertEquals(get_class($users), 'Zrails_Db_Table_Rowset');
        $this->assertEquals($users->getCurrentPageNumber(), 1);
        $this->assertEquals($users->getPageRange(), 2);
        $this->assertEquals($users->getItems(), 2);
        $this->assertEquals($users->getItemCountPerPage(), 1);
        $this->assertEquals($users->current()->id, 1);

        $this->assertTrue($users->isMoreThanOnePage());
        $this->assertFalse($users->isLessThanOnePage());

        $this->assertEquals(get_class($users->getTable()), 'Test_Model_Users');
    }


    public function testGetReferenceByColumn()
    {
        $this->assertNull($this->users->getReferenceByColumn('name'));

        $this->assertEquals($this->usersPhotos->getReferenceByColumn('user_id'), array(
            'columns'           => array('user_id'),
            'refTableClass'     => 'Test_Model_Users',
            'refColumns'        => array('id'),
        ));
    }

    public function testHasReferenceByColumn()
    {
        $this->assertFalse($this->users->hasReferenceByColumn('name'));
        $this->assertTrue($this->usersPhotos->hasReferenceByColumn('user_id'));
    }

    public function testGetAdditionalFields()
    {
        $this->assertEquals($this->users->getAdditionalFields(), array());
    }

    //row tests
    public function testToString()
    {
        $this->assertEquals($this->users->findRow(1)->__toString(), 'jo');
    }

    public function testFindManyToManyRowset()
    {
        $category = $this->categories->findRow(1);
        $this->assertEquals(get_class($category), 'Test_Model_Category');
        $rowset = $category->findManyToManyRowset('Test_Model_Users_Photos', 'Test_Model_Categories_Photos');
        $this->assertEquals(get_class($rowset), 'Zrails_Db_Table_Rowset');
        $this->assertEquals(count($rowset), 4);
    }

    public function testFindManyToManyRowsetPages()
    {
        $category = $this->categories->findRow(1);
        $this->assertEquals(get_class($category), 'Test_Model_Category');
        $rowset = $category->findManyToManyRowsetPages('Test_Model_Users_Photos', 'Test_Model_Categories_Photos', null, null, '', 2, 3);
        $this->assertEquals(get_class($rowset), 'Zrails_Db_Table_Rowset');
        $this->assertEquals(count($rowset), 1);

        $this->assertEquals($rowset->getCurrentPageNumber(), 2);
        $this->assertEquals($rowset->getPageRange(), 2);
        $this->assertEquals($rowset->getItems(), 4);
        $this->assertEquals($rowset->getItemCountPerPage(), 3);
    }

    public function testDependentRowset()
    {
        $user = $this->users->findRow(1);
        $this->assertEquals(get_class($user), 'Test_Model_User');
        $rowset = $user->findDependentRowset('Test_Model_Users_Photos');
        $this->assertEquals(get_class($rowset), 'Zrails_Db_Table_Rowset');
        $this->assertEquals(count($rowset), 3);
    }

    public function testDependentRowsetPages()
    {
        $user = $this->users->findRow(1);
        $this->assertEquals(get_class($user), 'Test_Model_User');
        $rowset = $user->findDependentRowsetPages('Test_Model_Users_Photos', null, 'name', 2, 2);
        $this->assertEquals(get_class($rowset), 'Zrails_Db_Table_Rowset');
        $this->assertEquals(count($rowset), 1);

        $this->assertEquals($rowset->getCurrentPageNumber(), 2);
        $this->assertEquals($rowset->getPageRange(), 2);
        $this->assertEquals($rowset->getItems(), 3);
        $this->assertEquals($rowset->getItemCountPerPage(), 2);
    }

    public function testGetTable()
    {
        $user = $this->users->findRow(1);
        $this->assertEquals(get_class($user->getTable()), 'Test_Model_Users');
    }

    public function testGetAdditionalFieldsForRow()
    {
        $this->assertEquals($this->users->findRow(1)->getAdditionalFields(), array());
    }

    public function testProtectedGetClassName()
    {
        $this->assertEquals($this->users->findRow(1)->_getClassName('Users'), 'Test_Model_Users');
    }

    public function testProtectedGetViaClassByClassForManyReference()
    {
        $category = $this->categories->findRow(1);
        $this->assertEquals($category->_getViaClassByClassForManyReference('Test_Model_Users_Photos'), 'Test_Model_Categories_Photos');
    }


    // find test
    public function testFindUserPhotos()
    {
        $user = $this->users->findRow(1);
        $rowset = $user->findUsers_Photos();
        $this->assertEquals(count($rowset), 3);
    }

    public function testFindUserPhotosPages()
    {
        $user = $this->users->findRow(1);
        $rowset = $user->findUsers_PhotosPages(2, 2);
        $this->assertEquals(count($rowset), 1);
    }

    public function testFindUserPhotosPagesOrder()
    {
        $user = $this->users->findRow(1);
        $rowset = $user->findUsers_PhotosPagesOrderNameDesc(2, 2);
        $this->assertEquals(count($rowset), 1);
        $this->assertEquals($rowset->current()->name, 'jo_photo1');
    }

    public function testFindUserPhotosPagesOrderWhere()
    {
        $user = $this->users->findRow(1);
        $rowset = $user->findUsers_PhotosPagesOrderNameDescWhere("name='jo_photo2'", 2, 2);
        $this->assertEquals(count($rowset), 1);
        $this->assertEquals($rowset->current()->name, 'jo_photo2');
    }


    public function testFindMenyToManyUserPhotosPagesOrder()
    {
        $category = $this->categories->findRow(1);
        $rowset = $category->findUsers_PhotosViaCategories_PhotosPagesOrderNameDesc(2, 1);
        $this->assertEquals(count($rowset), 1);
        $this->assertEquals($rowset->current()->name, 'jo_photo3');
    }

    public function testFindMenyToManyUserPhotosPagesOrderWhere()
    {
        $category = $this->categories->findRow(1);
        $rowset = $category->findUsers_PhotosViaCategories_PhotosPagesOrderNameDescWhere("name='jo_photo2'", 1, 1);
        $this->assertEquals(count($rowset), 1);
        $this->assertEquals($rowset->current()->name, 'jo_photo2');
    }

    public function testInsert()
    {
        $category = $this->categories->createRow();
        $category->setFromArray(array(
            'name' => 'new',
        ));
        $category->save();
        $rowset = $category->findUsers_PhotosViaCategories_Photos();
        $this->assertEquals(count($rowset), 0);
    }

    public function testInsertWithDepends()
    {
        $category = $this->categories->createRow();
        $category->setFromArray(array(
            'name' => 'new',
            'Test_Model_Users_Photos' => array(1,3)
        ));
        $category->save();

        $rowset = $category->findUsers_PhotosViaCategories_Photos();
        $this->assertEquals(count($rowset), 2);
        $this->assertEquals($rowset->current()->id, 1);
        $rowset->next();
        $this->assertEquals($rowset->current()->id, 3);
    }

    public function testUpdateWithDep()
    {
        $category = $this->categories->findRow(1);
        $category->setFromArray(array(
            'name'=>'new',
            'Test_Model_Users_Photos' => array(1, 3)
        ))->save();

        $rowset = $category->findUsers_PhotosViaCategories_Photos();
        $this->assertEquals(count($rowset), 2);
        $this->assertEquals($rowset->current()->id, 1);
        $rowset->next();
        $this->assertEquals($rowset->current()->id, 3);
    }

    public function testDelete()
    {
        $category = $this->categories->createRow(array('name'=>'new'));
        $category->save();
        $id = $category->getId();
        $rowset = $this->categories->fetchAll('id=' . $id);
        $this->assertEquals(count($rowset), 1);
        $this->assertEquals($rowset->current()->getId(), $id);
        $category->delete();
        $rowset = $this->categories->fetchAll('id=' . $id);
        $this->assertEquals(count($rowset), 0);
    }

    public function testDeleteWithDependency()
    {
        $category = $this->categories->findRow(1);
        $category->delete();
        $rowset = $this->categoriesPhotos->fetchAll('category_id=1');
        $this->assertEquals(count($rowset), 0);
    }

    public function testDeleteNonSave()
    {
        $category = $this->categories->createRow(array('name'=>'new'));
        $category->delete();
    }
}

