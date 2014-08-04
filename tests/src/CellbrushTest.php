<?php

namespace Donquixote\Cellbrush\Tests;

use Donquixote\Cellbrush\Table;

class CellbrushTest extends \PHPUnit_Framework_TestCase {

  function testRegularTable() {
    $table = Table::create()
      ->addRowName('row0')
      ->addRowName('row1')
      ->addRowName('row2')
      ->addColName('col0')
      ->addColName('col1')
      ->addColName('col2')
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $table->thead()
      ->addRowName('head0')
      ->th('head0', 'col0', 'Column 0')
      ->th('head0', 'col1', 'Column 1')
      ->th('head0', 'col2', 'Column 2')
    ;
    $html = $table->render();

    $expected = <<<EOT
<table>
  <thead>
    <tr><th>Column 0</th><th>Column 1</th><th>Column 2</th></tr>
  </thead>
  <tbody>
    <tr><td>Diag 0</td><td></td><td></td></tr>
    <tr><td></td><td>Diag 1</td><td></td></tr>
    <tr><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $html);
  }

  function testNumericKeys() {
    $table = Table::create()
      ->addRowNames([0, 1, 2])
      ->addColNames([0, 1, 2])
      ->td(0, 0, 'Diag 0')
      ->td(1, 1, 'Diag 1')
      ->td(2, 2, 'Diag 2')
    ;
    $table->headRow()
      ->thMultiple(['Column 0', 'Column 1', 'Column 2'])
    ;
    $html = $table->render();

    $expected = <<<EOT
<table>
  <thead>
    <tr><th>Column 0</th><th>Column 1</th><th>Column 2</th></tr>
  </thead>
  <tbody>
    <tr><td>Diag 0</td><td></td><td></td></tr>
    <tr><td></td><td>Diag 1</td><td></td></tr>
    <tr><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $html);
  }

  function testFullRowspan() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('', 'col1', 'Full rowspan')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $expected = <<<EOT
<table>
  <tbody>
    <tr><td>Diag 0</td><td rowspan="3">Full rowspan</td><td></td></tr>
    <tr><td></td><td></td></tr>
    <tr><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testFullColspan() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', '', 'Full colspan')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $expected = <<<EOT
<table>
  <tbody>
    <tr><td>Diag 0</td><td></td><td></td></tr>
    <tr><td colspan="3">Full colspan</td></tr>
    <tr><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testColGroup() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
      ->addColGroup('cg', ['a', 'b', 'c'])
      ->td('row0', 'cg.a', 'GD 0.a')
      ->td('row0', 'cg.b', 'GD 0.b')
      ->td('row1', 'cg', 'Span')
      ->td('row2', 'cg.a', 'GD 3.a')
      ->td('row2', 'cg.c', 'GD 3.c')
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr><td>Diag 0</td><td></td><td></td><td>GD 0.a</td><td>GD 0.b</td><td></td></tr>
    <tr><td></td><td>Diag 1</td><td></td><td colspan="3">Span</td></tr>
    <tr><td></td><td></td><td>Diag 2</td><td>GD 3.a</td><td></td><td>GD 3.c</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testRowGroup() {
    $table = Table::create()
      ->addColNames(['legend', 'sublegend', 0, 1])
      ->addRowGroup('dimensions', ['width', 'height'])
      ->addRowName('price')
      ->th('dimensions', 'legend', 'Dimensions')
      ->th('dimensions.width', 'sublegend', 'Width')
      ->th('dimensions.height', 'sublegend', 'Height')
      ->th('price', 'legend', 'Price')
    ;
    $table->headRow()->thMultiple(['Product 0', 'Product 1']);
    $table->rowHandle('dimensions.width')->tdMultiple(['2cm', '5cm']);
    $table->rowHandle('dimensions.height')->tdMultiple(['14g', '22g']);
    $table->rowHandle('price')->tdMultiple(['7,- EUR', '5,22 EUR']);

    $expected = <<<EOT
<table>
  <thead>
    <tr><td></td><td></td><th>Product 0</th><th>Product 1</th></tr>
  </thead>
  <tbody>
    <tr><th rowspan="2">Dimensions</th><th>Width</th><td>2cm</td><td>5cm</td></tr>
    <tr><th>Height</th><td>14g</td><td>22g</td></tr>
    <tr><th>Price</th><td></td><td>7,- EUR</td><td>5,22 EUR</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testRowAndColGroups() {
    $table = Table::create()
      ->addColName('name')
      ->addColGroup('info', ['color', 'price'])
      ->addRowGroup('banana', ['description', 'info'])
      ->th('banana', 'name', 'Banana')
      ->td('banana.description', 'info', 'A yellow fruit.')
      ->td('banana.info', 'info.color', 'yellow')
      ->td('banana.info', 'info.price', '60 cent')
      ->addRowGroup('coconut', ['description', 'info'])
      ->th('coconut', 'name', 'Coconut')
      ->td('coconut.description', 'info', 'Has liquid inside.')
      ->td('coconut.info', 'info.color', 'brown')
      ->td('coconut.info', 'info.price', '3 dollar')
    ;
    $table->headRow()
      ->th('name', 'Name')
      ->th('info.color', 'Color')
      ->th('info.price', 'Price')
    ;

    $expected = <<<EOT
<table>
  <thead>
    <tr><th>Name</th><th>Color</th><th>Price</th></tr>
  </thead>
  <tbody>
    <tr><th rowspan="2">Banana</th><td colspan="2">A yellow fruit.</td></tr>
    <tr><td>yellow</td><td>60 cent</td></tr>
    <tr><th rowspan="2">Coconut</th><td colspan="2">Has liquid inside.</td></tr>
    <tr><td>brown</td><td>3 dollar</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testRowHandle() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $table->headRow()
      ->th('col0', 'Column 0')
      ->th('col1', 'Column 1')
      ->th('col2', 'Column 2')
    ;
    $expected = <<<EOT
<table>
  <thead>
    <tr><th>Column 0</th><th>Column 1</th><th>Column 2</th></tr>
  </thead>
  <tbody>
    <tr><td>Diag 0</td><td></td><td></td></tr>
    <tr><td></td><td>Diag 1</td><td></td></tr>
    <tr><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testColHandle() {
    $table = new Table();
    $table
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['legend', 'col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $table->colHandle('legend')
      ->th('row0', 'Row 0')
      ->th('row1', 'Row 1')
      ->th('row2', 'Row 2')
    ;
    $expected = <<<EOT
<table>
  <tbody>
    <tr><th>Row 0</th><td>Diag 0</td><td></td><td></td></tr>
    <tr><th>Row 1</th><td></td><td>Diag 1</td><td></td></tr>
    <tr><th>Row 2</th><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testRowAndColHandle() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['legend', 'col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
    ;
    $table->headRow()
      ->thMultiple(
        [
          'col0' => 'Column 0',
          'col1' => 'Column 1',
          'col2' => 'Column 2',
        ]
      )
    ;
    $table->colHandle('legend')
      ->thMultiple(
        [
          'row0' => 'Row 0',
          'row1' => 'Row 1',
          'row2' => 'Row 2',
        ]
      )
    ;
    $expected = <<<EOT
<table>
  <thead>
    <tr><td></td><th>Column 0</th><th>Column 1</th><th>Column 2</th></tr>
  </thead>
  <tbody>
    <tr><th>Row 0</th><td>Diag 0</td><td></td><td></td></tr>
    <tr><th>Row 1</th><td></td><td>Diag 1</td><td></td></tr>
    <tr><th>Row 2</th><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testCellRange() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Cell 0.0')
      ->td('row1', 'col0', 'Cell 1.0')
      ->td('row2', 'col0', 'Cell 2.0')
      ->td('row2', 'col1', 'Cell 2.1')
      ->td('row2', 'col2', 'Cell 2.2')
      ->td(['row0', 'row1'], ['col1', 'col2'], 'Range box')
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr><td>Cell 0.0</td><td rowspan="2" colspan="2">Range box</td></tr>
    <tr><td>Cell 1.0</td></tr>
    <tr><td>Cell 2.0</td><td>Cell 2.1</td><td>Cell 2.2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testCellRangeOverlap() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->th('row1', 'col1', 'Middle')
      ->td('row0', ['col0', 'col1'], '0.0 - 0.1')
      ->td('row2', ['col1', 'col2'], '2.1 - 2.2')
      ->td(['row1', 'row2'], 'col0', '1.0<br/>2.0')
      ->td(['row0', 'row1'], 'col2', '0.2<br/>1.2')
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr><td colspan="2">0.0 - 0.1</td><td rowspan="2">0.2<br/>1.2</td></tr>
    <tr><td rowspan="2">1.0<br/>2.0</td><th>Middle</th></tr>
    <tr><td colspan="2">2.1 - 2.2</td></tr>
  </tbody>
</table>

EOT;
    $this->assertEquals($expected, $table->render());
  }

  function testRowClass() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
      ->addRowClasses(
        [
          'row0' => 'rowClass0',
          'row1' => 'rowClass1',
          'row2' => 'rowClass2',
        ])
      ->addRowClass('row2', 'extraClass')
    ;
    $table->tbody()
      ->addRowClass('row1', 'otherClass')
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr class="rowClass0"><td>Diag 0</td><td></td><td></td></tr>
    <tr class="rowClass1 otherClass"><td></td><td>Diag 1</td><td></td></tr>
    <tr class="rowClass2 extraClass"><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testRowStripingOddEven() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2', 'row3', 'row4', 'row5'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
      ->td('row3', 'col0', 'Diag 0')
      ->td('row4', 'col1', 'Diag 1')
      ->td('row5', 'col2', 'Diag 2')
      // Basic odd/even striping
      ->addRowStriping()
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr class="odd"><td>Diag 0</td><td></td><td></td></tr>
    <tr class="even"><td></td><td>Diag 1</td><td></td></tr>
    <tr class="odd"><td></td><td></td><td>Diag 2</td></tr>
    <tr class="even"><td>Diag 0</td><td></td><td></td></tr>
    <tr class="odd"><td></td><td>Diag 1</td><td></td></tr>
    <tr class="even"><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testRowStripingAdvanced() {
    $table = Table::create()
      ->addRowNames(['row0', 'row1', 'row2', 'row3', 'row4', 'row5'])
      ->addColNames(['col0', 'col1', 'col2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
      ->td('row3', 'col0', 'Diag 0')
      ->td('row4', 'col1', 'Diag 1')
      ->td('row5', 'col2', 'Diag 2')
      ->addRowClass('row1', 'extraClass')
      // Basic odd/even striping
      ->addRowStriping()
      // 3-way striping.
      ->addRowStriping(['1of3', NULL, '3of3'])
    ;

    $expected = <<<EOT
<table>
  <tbody>
    <tr class="odd 1of3"><td>Diag 0</td><td></td><td></td></tr>
    <tr class="extraClass even"><td></td><td>Diag 1</td><td></td></tr>
    <tr class="odd 3of3"><td></td><td></td><td>Diag 2</td></tr>
    <tr class="even 1of3"><td>Diag 0</td><td></td><td></td></tr>
    <tr class="odd"><td></td><td>Diag 1</td><td></td></tr>
    <tr class="even 3of3"><td></td><td></td><td>Diag 2</td></tr>
  </tbody>
</table>

EOT;

    $this->assertEquals($expected, $table->render());
  }

  function testColClass() {
    $table = Table::create()
      ->addColNames(['col0', 'col1', 'col2'])
      ->addRowNames(['row0', 'row1', 'row2'])
      ->td('row0', 'col0', 'Diag 0')
      ->td('row1', 'col1', 'Diag 1')
      ->td('row2', 'col2', 'Diag 2')
      // A column class for all table sections, including thead.
      ->addColClass('col0', 'colClass0')
      ->addColClasses(
        [
          'col0' => 'extraColClass0',
          'col1' => 'extraColClass1',
        ])
    ;
    $table->headRow()
      ->th('col0', 'H0')
      ->th('col1', 'H1')
      ->th('col2', 'H2')
    ;
    $table->tbody()
      // A column class that is only for the tbody section, not for thead.
      ->addColClass('col0', 'tbodyColClass0')
      ->addColClasses(
        [
          'col0' => 'tbodyExtraColClass0',
          'col1' => 'tbodyExtraColClass1',
        ])
    ;

    $expected = <<<EOT
<table>
  <thead>
    <tr><th class="colClass0 extraColClass0">H0</th><th class="extraColClass1">H1</th><th>H2</th></tr>
  </thead>
  <tbody>
    <tr>
      <td class="colClass0 extraColClass0 tbodyColClass0 tbodyExtraColClass0">Diag 0</td>
      <td class="extraColClass1 tbodyExtraColClass1"></td>
      <td></td>
    </tr>
    <tr>
      <td class="colClass0 extraColClass0 tbodyColClass0 tbodyExtraColClass0"></td>
      <td class="extraColClass1 tbodyExtraColClass1">Diag 1</td>
      <td></td>
    </tr>
    <tr>
      <td class="colClass0 extraColClass0 tbodyColClass0 tbodyExtraColClass0"></td>
      <td class="extraColClass1 tbodyExtraColClass1"></td>
      <td>Diag 2</td>
    </tr>
  </tbody>
</table>

EOT;

    $this->assertXmlStringEqualsXmlString($expected, $table->render());
  }

} 
