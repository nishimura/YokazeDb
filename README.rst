====================
YokazeDB: PHP O/R Mapping Libraries
====================

YokazeDb is simple O/R mapping libraries.


Usage
=====
Add ``cache`` directory in project directory::

   cd public_html
   mkdir cache
   chmod o+w cache

Include ``Factory.php`` file::

   cat > index.php
   <?php
   require_once 'YokazeDb/Factory.php';
   $factory = new YokazeDb_Factory();
   $factory->setDsn('pgsql:host=localhost;dbname=db;user=user;password=pass');

Get a row from item table::

   $orm = $factory->create('item');
   $aVo = $orm->getVo($primaryKey);
   $otherVo = $orm->getVo(array('name' => 'Foo Bar'));

Get rows from item table::

   $iterator = $factory->create('items');

Get rows by sql file::

   $iterator = $factory->create('Sql_Items');
   $iterator->setParams($itemId)->setReplacements('and name is not null');

   mkdir Sql
   cat > Sql/Items.sql
   select * from items
   where id = ?
   %s


With Yokaze framework::

   $t = new Yokaze_Parser();
   $r = new Yokaze_Request();
   $r->iterator = $factory->create('items');
   $r->pager = YokazeDb_Pager($r->iterator, 10);
   $t->show($r);

   <ul>
     <li class="loop:iterator:item">
       {item.id}: {item.name}
     </li>
   </ul>
   <div>
     {pager:h}
   </div>


