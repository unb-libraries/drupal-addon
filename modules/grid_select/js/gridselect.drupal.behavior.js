(function ($, Drupal) {
  Drupal.behaviors.gridSelect = {
    attach: function attach(context, settings) {
      $(context)
        .find('table.grid-select input.column-select')
        .each(function (cid, columnSelector) {
          Drupal.prepareColumnSelector($(columnSelector));
        });
      $(context)
        .find('table.grid-select input.row-select')
        .each(function (rid, rowSelector) {
          Drupal.prepareRowSelector($(rowSelector));
        });
      $(context)
        .find('table.grid-select input.cell-select')
        .each(function (cid, cellSelector) {
          Drupal.prepareCellSelector($(cellSelector));
        });
    }
  };

  // Select columns
  Drupal.prepareColumnSelector = function (columnSelector) {
    let column = columnSelector.data('column');
    let table = $(columnSelector.parents('table')[0]);
    columnSelector.on('change', Drupal.columnSelectorChanged);
    if (Drupal.isColumnSelected(column, table)) {
      columnSelector[0].checked = true;
    }
  }

  Drupal.columnSelectorChanged = function(event) {
    Drupal.selectWholeColumn($(event.currentTarget))
  }

  Drupal.selectWholeColumn = function(columnSelector) {
    let column = columnSelector.data('column');
    let table = $(columnSelector.parents('table')[0]);
    Drupal.columnCellSelectors(column, table)
      .each(function (index, cellSelector) {
        cellSelector.checked = columnSelector[0].checked;
        let row = $(cellSelector).data('row');
        Drupal.rowSelector(row, table)[0].checked = Drupal.isRowSelected(row, table);
      });
  }

  Drupal.columnCellSelectors = function(index, table) {
    return table
      .find('.cell-select[data-column="' + index + '"]');
  }

  // Select rows
  Drupal.prepareRowSelector = function (rowSelector) {
    let row = rowSelector.data('row');
    let table = $(rowSelector.parents('table')[0]);
    rowSelector.on('change', Drupal.rowSelectorChanged);
    if (Drupal.isRowSelected(row, table)) {
      rowSelector[0].checked = true;
    }
  }

  Drupal.rowSelectorChanged = function(event) {
    Drupal.selectWholeRow($(event.currentTarget));
  }

  Drupal.selectWholeRow = function(rowSelector) {
    let row = rowSelector.data('row');
    let table = $(rowSelector.parents('table')[0]);
    Drupal.rowCellSelectors(row, table)
      .each(function(index, cellSelector) {
        cellSelector.checked = rowSelector[0].checked;
        let column = $(cellSelector).data('column');
        Drupal.columnSelector(column, table)[0].checked = Drupal.isColumnSelected(column, table);
      });
  }

  Drupal.rowCellSelectors = function(index, table) {
    return table
      .find('.cell-select[data-row="' + index + '"]');
  }

  // Select cells
  Drupal.prepareCellSelector = function(cellSelector) {
    cellSelector.on('change', Drupal.cellSelectorChanged);
  }

  Drupal.cellSelectorChanged = function(event) {
    let cellSelector = $(event.currentTarget);
    let row = cellSelector.data('row');
    let column = cellSelector.data('column');
    let table = $(cellSelector.parents('table')[0]);

    Drupal.columnSelector(column, table)[0].checked = Drupal.isColumnSelected(column, table);
    Drupal.rowSelector(row, table)[0].checked = Drupal.isRowSelected(row, table);
  }

  Drupal.isColumnSelected = function(column, table) {
    return Drupal.columnCellSelectors(column, table)
      .filter(function (index, cellSelector) {
        return !cellSelector.checked;
      })
      .length === 0;
  }

  Drupal.columnSelector = function(column, table) {
    return table
      .find('.column-select[data-column="' + column + '"]');
  }

  Drupal.isRowSelected = function(row, table) {
    return Drupal.rowCellSelectors(row, table)
      .filter(function (index, cellSelector) {
        return !cellSelector.checked;
      })
      .length === 0;
  }

  Drupal.rowSelector = function (row, table) {
    return table
      .find('.row-select[data-row="' + row + '"]');
  }

})(jQuery, Drupal);