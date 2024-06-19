<style rel="stylesheet" type="text/css">
    @media (min-width: 640px) {
        .container {
            max-width: 640px
        }
    }

    @media (min-width: 768px) {
        .container {
            max-width: 768px
        }
    }

    @media (min-width: 1024px) {
        .container {
            max-width: 1024px
        }
    }

    @media (min-width: 1280px) {
        .container {
            max-width: 1280px
        }
    }

    @media (min-width: 1536px) {
        .container {
            max-width: 1536px
        }
    }

    @media (min-width: 640px) {
        .sm\:max-w-xl {
            max-width: 36rem
        }

        .sm\:rounded-lg {
            border-radius: 0.5rem
        }

        .sm\:text-xl {
            font-size: 1.25rem;
            line-height: 1.75rem
        }
    }

    @media (min-width: 768px) {
        .md\:w-2\/3 {
            width: 66.666667%
        }

        .md\:p-4 {
            padding: 1rem
        }
    }

    @media (min-width: 1024px) {
        .lg\:w-3\/4 {
            width: 75%
        }
    }
</style>
<x-profile.layout>

    <div class="w-full px-6 pb-8 mt-8 sm:max-w-xl sm:rounded-lg">
        <h2 class="pl-6 text-2xl font-bold sm:text-xl">Certificates</h2>
    </div>

    <div class="container">
        @if ($certificates->count() > 0)
            <div class="flex gap-3">
                @foreach ($certificates as $certificate)
                    <div style="min-height:200px;height:331.234px;display:flex;flex-direction:column;position:relative;">
                        <div class="shadow border"
                            style="border-radius:16px;background:rgb(255, 255, 255) none repeat scroll 0% 0% / auto padding-box border-box;transition:all 0.3s cubic-bezier(0, 0, 0.5, 1) 0s;position:relative;transform:matrix(1, 0, 0, 1, 0, 0);--card-padding: calc(8px);--card-border-radius: 16px;--card-box-shadow: 0 0 40px -8px rgb(0 0 0 / 16%),0 0 24px -16px rgb(0 0 0 / 16%);--card-hover-transform: scale3d(1.03, 1.03, 1.08) translate3d(0.1rem, -0.25rem, 20rem);--card-title-line-clamp: 3;--card-body-line-clamp: 3;--card-metadata-line-clamp: 2;--preview-aspect-ratio: 16/9;--preview-border-radius: 8px;--grid-max-width: 470px;--grid-min-width: 230px;--grid-border: 2px;--list-max-width: 100%;--list-min-width: 320px;--list-img-size: 80px;--list-img-size-xs: 64px;--list-img-size-lg: 100%;">
                            <div style="position:relative;height: auto;display:flex;flex-direction:column;flex:1 1 0%;">
                                <div style="display:flex;flex-direction:column;flex:1 1 0%;padding:8px;">
                                    <div style="position:relative;">
                                        <div
                                            style="border-radius:8px;margin-bottom:8px;aspect-ratio:16 / 9;overflow:hidden;position:relative;">
                                            <img src="https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/https://coursera-course-photos.s3.amazonaws.com/e7/ea896ef5cf43859b38b86324d64a0d/learn-imba.png?auto=format%2Ccompress%2C%20enhance&amp;dpr=1&amp;w=265&amp;h=204&amp;fit=crop&amp;q=50"
                                                style="display: block; max-width: none; max-height: none; min-width: 100%; vertical-align: middle; box-sizing: border-box; border: 0px none rgb(55, 58, 60); height: 142.734px; width: 100%; object-fit: cover; -webkit-font-smoothing: antialiased;"
                                                alt="">
                                        </div>
                                    </div>
                                    <div style="display:flex;flex-direction:column;flex:1 1 0%;gap:16px;padding:8px;">
                                        <div style="display:flex;flex-direction:column;gap:8px;">
                                            <div style="">
                                                <a data-click-key="unified_description_page.consumer_course_page.click.collection_product_card"
                                                    data-click-value="{&quot;collectionId&quot;:&quot;recommendations&quot;,&quot;href&quot;:&quot;/learn/learn-imba&quot;,&quot;id&quot;:&quot;aluh5mR3Ee6vIRJJdXRzxQ&quot;,&quot;item&quot;:{&quot;__typename&quot;:&quot;DescriptionPage_CollectionEntity&quot;,&quot;id&quot;:&quot;aluh5mR3Ee6vIRJJdXRzxQ&quot;,&quot;imageUrl&quot;:&quot;https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/https://coursera-course-photos.s3.amazonaws.com/e7/ea896ef5cf43859b38b86324d64a0d/learn-imba.png&quot;,&quot;link&quot;:&quot;/learn/learn-imba&quot;,&quot;name&quot;:&quot;Learn Imba&quot;,&quot;partnerIds&quot;:[&quot;1324&quot;],&quot;partnerLogos&quot;:[&quot;http://coursera-university-assets.s3.amazonaws.com/40/d548ef33b3401f942edc9c24840b52/social.png&quot;],&quot;partners&quot;:[{&quot;__typename&quot;:&quot;DescriptionPage_Partner&quot;,&quot;accentColor&quot;:&quot;#4a35ce&quot;,&quot;classLogo&quot;:&quot;https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/http://coursera-university-assets.s3.amazonaws.com/97/f444df8f4e4d8297bd419f9383cf4f/Frame-915.png&quot;,&quot;description&quot;:&quot;Scrimba is an interactive code-learning platform with over a million users from all over the world. They feature highly interactive and engaging courses about programming and web development.&quot;,&quot;id&quot;:&quot;1324&quot;,&quot;instructorIds&quot;:[&quot;130316767&quot;,&quot;138763027&quot;,&quot;140543294&quot;,&quot;130254563&quot;,&quot;148296386&quot;,&quot;141020737&quot;,&quot;130102196&quot;,&quot;121343301&quot;,&quot;140698289&quot;,&quot;88610845&quot;,&quot;136602877&quot;,&quot;121495947&quot;,&quot;115377598&quot;,&quot;119841372&quot;],&quot;logo&quot;:&quot;http://coursera-university-assets.s3.amazonaws.com/40/d548ef33b3401f942edc9c24840b52/social.png&quot;,&quot;name&quot;:&quot;Scrimba&quot;,&quot;partnerMarketingBlurb&quot;:null,&quot;primaryColor&quot;:&quot;#1b105a&quot;,&quot;primaryLogo&quot;:&quot;https://d3njjcbhbojbot.cloudfront.net/api/utilities/v1/imageproxy/http://coursera-university-assets.s3.amazonaws.com/97/f444df8f4e4d8297bd419f9383cf4f/Frame-915.png&quot;,&quot;productBrandingLogo&quot;:&quot;http://coursera-university-assets.s3.amazonaws.com/88/4d6525469046b2912967e46a4e8917/svg.svg&quot;,&quot;rectangularLogo&quot;:&quot;http://coursera-university-assets.s3.amazonaws.com/53/02aa3c5fb84541bc94f2ca98dd84a9/svg.svg&quot;,&quot;secondaryColor&quot;:&quot;#3D4E5D&quot;,&quot;shortName&quot;:&quot;scrimba&quot;,&quot;squareLogo&quot;:&quot;http://coursera-university-assets.s3.amazonaws.com/40/d548ef33b3401f942edc9c24840b52/social.png&quot;,&quot;website&quot;:&quot;https://scrimba.com/&quot;}],&quot;productType&quot;:&quot;COURSE&quot;,&quot;slug&quot;:&quot;learn-imba&quot;},&quot;itemIndex&quot;:1,&quot;namespace&quot;:{&quot;action&quot;:&quot;click&quot;,&quot;app&quot;:&quot;unified_description_page&quot;,&quot;component&quot;:&quot;collection_product_card&quot;,&quot;page&quot;:&quot;consumer_course_page&quot;},&quot;schema_type&quot;:&quot;FRONTEND&quot;}"
                                                    data-track="true" data-track-app="unified_description_page"
                                                    data-track-page="consumer_course_page" data-track-action="click"
                                                    data-track-component="collection_product_card"
                                                    data-track-href="/learn/learn-imba"
                                                    href="{{ route('certificate_download', $certificate->id) }}"
                                                    aria-label="Learn Imba Course by Scrimba,"
                                                    style="-webkit-font-smoothing: antialiased; touch-action: manipulation; color: rgb(31, 31, 31); text-decoration: none solid rgb(31, 31, 31); box-sizing: border-box; background: none 0% 0% / auto repeat scroll padding-box border-box rgba(0, 0, 0, 0); vertical-align: baseline; display: block; padding: 0px; letter-spacing: normal; height: 20px; margin: 0px;">
                                                    <h3
                                                        style="max-width: 100%; margin: 0px; padding: 0px; -webkit-font-smoothing: antialiased; box-sizing: border-box; color: rgb(31, 31, 31); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; font: 600 16px / 20px &quot;Source Sans Pro&quot;, Arial, sans-serif; letter-spacing: -0.048px;">
                                                        {{ $certificate->title }}
                                                    </h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow w-100">
                <div style="border:2px solid rgb(229, 231, 232);border-radius:8px;margin-top: 20px;">
                    <div style="width: 100%;display:flex;flex-wrap:wrap;">
                        <div style="flex-grow:0;max-width:58.3333%;flex-basis:58.3333%;margin:0px;">
                            <div style="padding:32px;">
                                <h2
                                    style="max-width:100%;margin:0px 0px 16px;padding:0px;font-size:28px;line-height:36px;font-family:'Source Sans Pro', Arial, sans-serif;-webkit-font-smoothing:antialiased;margin-top:0px;margin-bottom:16px;box-sizing:border-box;font-weight:600;color:rgb(31, 31, 31);font:600 28px / 36px 'Source Sans Pro', Arial, sans-serif;letter-spacing:-0.1px;">
                                    Earn a career certificate</h2>
                                <p
                                    style="max-width:100%;margin-bottom:0px;font-size:16px;line-height:24px;font-family:'Source Sans Pro', Arial, sans-serif;-webkit-font-smoothing:antialiased;margin-top:0px;box-sizing:border-box;margin:0px;font:16px / 24px 'Source Sans Pro', Arial, sans-serif;letter-spacing:normal;color:rgb(31, 31, 31);">
                                    Add this credential to your LinkedIn profile, resume, or CV</p>
                                <p
                                    style="max-width:100%;margin-bottom:0px;font-size:16px;line-height:24px;font-family:'Source Sans Pro', Arial, sans-serif;-webkit-font-smoothing:antialiased;margin-top:0px;box-sizing:border-box;margin:0px;font:16px / 24px 'Source Sans Pro', Arial, sans-serif;letter-spacing:normal;color:rgb(31, 31, 31);">
                                    Share it on social media and in your performance review</p>
                                <a href="{{ route('courses_show') }}" class="mt-3 btn btn-primary">Get Your
                                    Certificates</a>
                            </div>
                        </div>
                        <div
                            style="flex-grow:0;max-width:41.6667%;flex-basis:41.6667%;min-height:168px;position:relative;margin:0px;">
                            <div style="position:absolute;right:0px;top:0px;height:168px;width: 96%;overflow:hidden;">
                                <div
                                    style="position:absolute;height:336px;width: 200%;background-color:rgb(235, 243, 255);opacity:0.5;clip-path:ellipse(30% 100% at 30% 10%);">
                                </div>
                            </div>
                            <div
                                style="position:absolute;right:43px;top:84px;transform:matrix(1, 0, 0, 1, 0, -107.5);height:215px;width: 333px;box-shadow:rgba(0, 0, 0, 0.2) 0px 4px 20px 0px;">
                                <div style="position:relative;width: 333px;padding-bottom:215px;height:215px;">
                                    <div style="box-sizing:border-box;"><img
                                            src="{{ url('images/certificates-image.png') }}"
                                            alt="Coursera Career Certificate"
                                            style="max-width: 333px; max-height: 215px;max-height:215px;-webkit-font-smoothing:antialiased;vertical-align:middle;box-sizing:border-box;border-style:none;border:0px none rgb(55, 58, 60);" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

</x-profile.layout>
