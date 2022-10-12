import './page/sw-cms-pages-list';
import './page/sw-cms-pages-detail';
import './snippet/en-GB.json';
const { Module } = Shopware;

Module.register('sw-cms-pages', {
    type: 'plugin',
    name: 'cms-pages',
    entity: 'cms_pages_detail',
    color: 'rgb(255, 104, 180)',
    icon: 'default-shopping-paper-bag-product',

    routes: {
        list: {
            component: 'sw-cms-pages-list',
            path: 'list'
        },
        detail: {
            component: 'sw-cms-pages-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'sw.cms.pages.list',
                privilege: 'cms_pages_detail.viewer',
            },
            props: {
                default(route) {
                    return {
                        pageId: route.params.id,
                    };
                },
            },
        },
        create: {
            component: 'sw-cms-pages-detail',
            path: 'create',
            meta: {
                parentPath: 'sw.cms.pages.list',
                privilege: 'cms_pages_detail.creator',
            },
        },
    },
    navigation: [{
        label: 'Cms Pages',
        color: 'rgb(255, 104, 180)',
        path: 'sw.cms.pages.list',
        parent: 'sw-content',
        icon: 'default-shopping-paper-bag-product',
        position: 100
    }],
});

