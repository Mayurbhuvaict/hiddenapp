import template from './sw-cms-pages-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('sw-cms-pages-list', {
    template,
    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            pages: null,
            isLoading: true,
            sortBy: 'title',
            sortDirection: 'ASC',
            total: 0,
            searchConfigEntity: 'cms_pages_overview',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(),
        };
    },

    computed: {
        pageRepository() {
            return this.repositoryFactory.create('cms_pages_detail');
        },
        CmsPageOverviewRepository() {
            return this.repositoryFactory.create('cms_pages_overview');
        },

        cmsColumns() {
            return [{
                property: 'title',
                dataIndex: 'title',
                allowResize: true,
                routerLink: 'sw.cms.pages.detail',
                label: 'sw-cms-pages.list.title',
                primary: true,
            },
                {
                    property: 'cmsPagesOverview.name',
                    allowResize: true,
                    label: 'sw-cms-pages.list.page',
                    // primary: true,
                    useCustomSort: false,
                },
            ];
        },

        pageCriteria() {
            const pageCriteria = new Criteria(this.page, this.limit);
            pageCriteria.setTerm(this.term);
            pageCriteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection, this.naturalSorting));

            return pageCriteria;
        },
    },

    methods: {
        onChangeLanguage(languageId) {
            this.getList(languageId);
        },

        async getList() {
            this.isLoading = true;

            const criteria = await this.addQueryScores(this.term, this.pageCriteria);
            if (!this.entitySearchable) {

                this.isLoading = false;
                this.total = 0;
                return false;
            }
            if (this.freshSearchTerm) {
                criteria.resetSorting();
            }

            return this.pageRepository.search(criteria)
                .then(searchResult => {
                    this.pages = searchResult;
                    this.total = searchResult.total;

                    // add new column
                    for (let i = 0; i < this.pages.length; i++) {
                        let pageId = this.pages[i].pageId;
                        let criteria = new Criteria();
                        criteria.addFilter(Criteria.equals('id', pageId));
                        this.CmsPageOverviewRepository
                            .search(criteria, Shopware.Context.api)
                            .then((result) => {
                                this.pages[i].cmsPage = result[0].pageName;
                            });
                    }
                    this.isLoading = false;
                });
        },

        updateTotal({ total }) {
            this.total = total;
        },
    },
});
