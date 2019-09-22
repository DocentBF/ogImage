var ogImage = function (config) {
    config = config || {};
    ogImage.superclass.constructor.call(this, config);
};
Ext.extend(ogImage, Ext.Component, {
    page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('ogimage', ogImage);

ogImage = new ogImage();