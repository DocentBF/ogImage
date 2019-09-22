ogImage.page.Home = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'ogimage-panel-home',
            renderTo: 'ogimage-panel-home-div'
        }]
    });
    ogImage.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(ogImage.page.Home, MODx.Component);
Ext.reg('ogimage-page-home', ogImage.page.Home);