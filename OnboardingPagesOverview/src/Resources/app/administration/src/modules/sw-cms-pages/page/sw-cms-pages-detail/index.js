import template from './sw-cms-pages-detail.html.twig';

const { Component, Mixin, Data: { Criteria } } = Shopware;

const { mapPropertyErrors, mapState } = Shopware.Component.getComponentHelper();

Component.register('sw-cms-pages-detail', {
    template,

    inject: ['repositoryFactory', 'acl'],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
        Mixin.getByName('discard-detail-page-changes')('cmsPages'),
    ],

    props: {
        pageId: {
            type: String,
            required: false,
            default: null,
        },
        name:{
            type: String,
            required: false,
            default: null,
        }
    },


    data() {
        return {
            cmsPagesOverview : {},
            cmsIds : [],
            cmsPages: null,
            customFieldSets: [],
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier),
        };
    },

    computed: {
        ...mapPropertyErrors('cmsPages', [
            'cmsPageId',
        ]),

        cmsPageId: {
            get() {
                return this.cmsPages.pageId;
            },
            set(cmsPageId) {
                this.cmsPages.pageId = cmsPageId;
            },
        },
        cmsPageCriteria() {
            const pageCriteria = new Criteria(this.page, this.limit);
            const idCriteria = new Criteria(this.page, this.limit);
            this.pageRepository.search(pageCriteria, Shopware.Context.api)
                .then((result) => {
                    result.map(function(val, index){
                        idCriteria.addFilter(Criteria.not('and', [Criteria.equals('id', val['pageId'])]));

                })
            });
            return idCriteria;
        },

        identifier() {
            return this.placeholder(this.cmsPages, 'title');
        },

        cmsIsLoading() {
            return this.isLoading || this.cmsPages == null;
        },

        pageRepository() {
            return this.repositoryFactory.create('cms_pages_detail');
        },

        cmsPageOverviewRepository(){
          return this.repositoryFactory.create('cms_pages_overview');
        },

        mediaRepository() {
            return this.repositoryFactory.create('media');
        },

        customFieldSetRepository() {
            return this.repositoryFactory.create('custom_field_set');
        },

        customFieldSetCriteria() {
            const criteria = new Criteria(1, null);
            criteria.addFilter(
                Criteria.equals('relations.entityName', 'cms_pages_detail'),
            );
            return criteria;
        },
        mediaUploadTag() {
            return `sw-cms-pages-detail--${this.cmsPages.id}`;
        },

        tooltipSave() {
            if (this.acl.can('cms_pages_detail.editor')) {
                const systemKey = this.$device.getSystemKey();

                return {
                    message: `${systemKey} + S`,
                    appearance: 'light',
                };
            }

            return {
                showDelay: 300,
                message: this.$tc('sw-privileges.tooltip.warning'),
                showOnDisabledElements: true,
            };
        },

        tooltipCancel() {
            return {
                message: 'ESC',
                appearance: 'light',
            };
        },
    },

    watch: {
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.pageId) {
                this.loadEntityData();

                return;
            }
            Shopware.State.commit('context/resetLanguageToDefault');
            this.cmsPages = this.pageRepository.create();


        },
        loadEntityData() {
            this.isLoading = true;
            this.pageRepository.get(this.pageId).then((pageData) => {
                this.isLoading = false;
                this.cmsPages = pageData;
            });
            this.customFieldSetRepository
                .search(this.customFieldSetCriteria)
                .then((result) => {
                    this.customFieldSets = result;
                });

        },
        abortOnLanguageChange() {
            return this.pageRepository.hasChanges(this.cmsPages);
        },

        onUpdatePage(){
          if(this.$route.params.id){
              return 1;
          }else{
              return 0;
          }
        },

        saveOnLanguageChange() {
            return this.onSave();
        },
        onChangeLanguage() {
            this.loadEntityData();
        },
        setMediaItem({ targetId }) {
            this.cmsPages.mediaId = targetId;
        },

        onDropMedia(dragData) {
            this.setMediaItem({ targetId: dragData.id });
        },


        setMediaFromSidebar(media) {
            this.cmsPages.mediaId = media.id;
        },

        onUnlinkLogo() {
            this.cmsPages.mediaId = null;
        },

        openMediaSidebar() {
            this.$refs.mediaSidebarItem.openContent();
        },
        onSave() {
            this.isLoading = true;
            this.pageRepository.save(this.cmsPages).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;
                if (this.pageId === null) {
                    this.$router.push({name: 'sw.cms.pages.list'});
                    this.$router.push({ name: 'sw.cms.pages.detail', params: { id: this.cmsPages.id} });
                    window.location.reload();
                    return;
                }

                this.loadEntityData();
            }).catch((exception) => {
                this.isLoading = false;
                this.createNotificationError({
                    message: this.$tc(
                        'global.notification.notificationSaveErrorMessageRequiredFieldsInvalid',
                    ),
                });
                throw exception;
            });
        },
        onCancel() {
            this.$router.push({ name: 'sw.cms.pages.list' });
        },
    },
});
