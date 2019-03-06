<?php

namespace AlanZhao\Cellbrush\Table;

use AlanZhao\Cellbrush\Columns\ColumnClassesInterface;
use AlanZhao\Cellbrush\Columns\TableColumnsInterface;
use AlanZhao\Cellbrush\Html\MutableAttributesInterface;
use AlanZhao\Cellbrush\TSection\TableSectionStructureInterface;

interface TableInterface extends
  MutableAttributesInterface,
  TableSectionStructureInterface,
  TableColumnsInterface,
  ColumnClassesInterface {

}
