/**
 * Part of the dsa blacksmith.
 *
 * @package JavaScript
 * @author  friend8 <map@wafriv.de>
 * @license https://www.gnu.org/licenses/lgpl.html LGPLv3
 */
(function($, window) {
    $.widget('dsabs.popupBlueprint', $.dsabs.popup, {
        options: {
            templates: {
                container: $('<div style="display: none;"></div>'),
                table: $('<table class="showBlueprint collapse"></table>'),
                thead: $('<thead></thead>'),
                tbody: $('<tbody></tbody>'),
                row: $('<tr></tr>'),
                headColumn: $('<th></th>'),
                bodyColumn: $('<td></td>')
            },
            dialog: {
                title: window.translations.showBlueprint,
                width: 900
            }
        },
        _createContent: function (callback) {
            $.post(this._config.targetUrl, $.proxy(function (response) {
                if (!response.ok)
                {
                    throw new 'Could not fetch blueprint.';
                    return;
                }

                var container,
                    table,
                    thead,
                    tbody,
                    headRow,
                    bodyRow,
                    column,
                    columns = {
                        meleeWeapon: {
                            name: 'name',
                            hp: 'hitPoints',
                            weight: 'weight',
                            bf: 'breakFactor',
                            ini: 'initiative',
                            price: 'price',
                            wm: 'weaponModificator',
                            notes: 'notes',
                            time: 'time'
                        },
                        rangedWeapon: {
                            name: 'name',
                            hp: 'hitPoints',
                            weight: 'weight',
                            price: 'price',
                            notes: 'notes',
                            time: 'time'
                        },
                        projectile: {
                            name: 'name',
                            projectileForItem: 'projectileForItem',
                            time: 'time'
                        }
                    },
                    infoContainer;

                table = this.options.templates.table.clone();
                thead = this.options.templates.thead.clone();
                headRow = this.options.templates.row.clone();

                $.each(columns[response.data.type], $.proxy(function (key, value) {
                    column = this.options.templates.headColumn.clone();
                    column.addClass(value);
                    column.text(window.translations[key]);
                    headRow.append(column);
                }, this));

                thead.append(headRow);
                table.append(thead);
                tbody = this.options.templates.tbody.clone();
                bodyRow = this.options.templates.row.clone();

                $.each(columns[response.data.type], $.proxy(function (key, value) {
                    column = this.options.templates.bodyColumn.clone();
                    column.addClass(value);
                    column.text(response.data[value]);
                    bodyRow.append(column);
                }, this));

                tbody.append(bodyRow);
                table.append(tbody);
                container = this.options.templates.container.clone();
                container.append(table);

                infoContainer = $('<div class="infoContainer"></div>');
                infoContainer.append(window.translations['improvisationalNote'] + ' = ' + window.translations['improvisational'] + '<br />');
                infoContainer.append(window.translations['twoHandedNote'] + ' = ' + window.translations['twoHanded'] + '<br />');
                infoContainer.append(window.translations['privilegedNote'] + ' = ' + window.translations['privileged'] + '<br />');
                infoContainer.append(window.translations['bonusRangedFightValueNote'] + ' = ' + window.translations['bonusRangedFightValue'] + '<br />');
                infoContainer.append(window.translations['reducePhysicalStrengthRequirementNote'] + ' = ' + window.translations['reducePhysicalStrengthRequirement']);

                container.append(infoContainer);

                this.options.content = container;

                if (typeof callback === 'function')
                {
                    callback();
                }
            }, this), 'json');
        }
    });
}(jQuery, window));