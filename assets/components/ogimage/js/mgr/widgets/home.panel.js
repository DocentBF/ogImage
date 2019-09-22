ogImage.panel.Home = function (config) {
    config = config || {};
    Ext.apply(config, {
        baseCls: 'modx-formpanel',
        layout: 'anchor',
        /*
         stateful: true,
         stateId: 'ogimage-panel-home',
         stateEvents: ['tabchange'],
         getState:function() {return {activeTab:this.items.indexOf(this.getActiveTab())};},
         */
        hideMode: 'offsets',
        items: [{
            html: '<h2>' + _('ogimage') + '</h2>',
            cls: '',
            style: {margin: '15px 0'}
        }, {
            xtype: 'modx-tabs',
            defaults: {border: false, autoHeight: true},
            border: true,
            hideMode: 'offsets',
            items: [{
                title: _('ogimage_items'),
                layout: 'anchor',
                items: [{
                    html: _('ogimage_intro_msg'),
                    cls: 'panel-desc',
                }, {
                    xtype: 'ogimage-grid-items',
                    cls: 'main-wrapper',
                }]
            }]
        }]
    });
    ogImage.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(ogImage.panel.Home, MODx.Panel);
Ext.reg('ogimage-panel-home', ogImage.panel.Home);
