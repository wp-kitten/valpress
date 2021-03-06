<?php

namespace App\Http\Controllers;

use App\Helpers\VPML;
use App\Models\Post;
use App\Models\PostStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;

class SiteController extends Controller
{
    /**
     * Render the website's homepage.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view( 'index' );
    }

    /**
     * Render the 404 error page
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function error404()
    {
        return view( '404' );
    }

    /**
     * Render the 500 error page
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function error500()
    {
        return view( '500' );
    }

    /**
     * Display a post type based on the provided slug. The App Locale will be updated accordingly to the language the post is assigned to.
     * @param string $slug
     * @return Application|RedirectResponse|\Illuminate\Http\Response|Redirector
     */
    public function post_view( string $slug )
    {
        //#! Get the current language ID
        $defaultLanguageID = VPML::getDefaultLanguageID();
        //#! Get the selected language in frontend
        $frontendLanguageID = vp_get_frontend_user_language_id();

        //#! Make sure the post is published if the current user is not allowed to "edit_private_posts"
        $_postStatuses = PostStatus::all();
        $postStatuses = [];
        if ( vp_current_user_can( 'edit_private_posts' ) ) {
            $postStatuses = Arr::pluck( $_postStatuses, 'id' );
        }
        else {
            $postStatuses[] = PostStatus::where( 'name', 'publish' )->first()->id;
        }

        //#! Check to see if we have a match for slug & $frontendLanguageID
        $thePost = Post::where( 'slug', $slug )->where( 'language_id', $frontendLanguageID );
        $postFound = $thePost->first();
        if ( vp_is_multilingual() ) {
            //#! Check to see if we have a translation for this post
            if ( !$postFound ) {
                $posts = Post::where( 'slug', $slug )->get();
                if ( $posts ) {
                    foreach ( $posts as $post ) {
                        $translatedPostID = $post->translated_post_id;

                        //#! Default language -> other language ( EN -> RO ) //
                        if ( empty( $translatedPostID ) ) {
                            $thePost = Post::where( 'translated_post_id', $post->id )->where( 'language_id', $frontendLanguageID )->first();
                            if ( !$thePost ) {
                                return $this->_not_found();
                            }
                            return redirect( vp_get_post_view_url( $thePost ) );
                        }

                        //#! Other language -> default language ( RO -> EN ) //
                        elseif ( $frontendLanguageID == $defaultLanguageID ) {
                            $thePost = Post::where( 'id', $post->translated_post_id )->where( 'language_id', $frontendLanguageID )->first();
                            if ( !$thePost ) {
                                return $this->_not_found();
                            }
                            return redirect( vp_get_post_view_url( $thePost ) );
                        }

                        //#! other language -> other language ( ES -> RO )
                        elseif ( !empty( $translatedPostID ) ) {
                            $thePost = Post::where( 'translated_post_id', $post->translated_post_id )->where( 'language_id', $frontendLanguageID )->first();
                            if ( !$thePost ) {
                                return $this->_not_found();
                            }
                            return redirect( vp_get_post_view_url( $thePost ) );
                        }
                        else {
                            return $this->_not_found();
                        }
                    }
                }
                else {
                    return $this->_not_found();
                }
            }
        }

        $thePost = $thePost->whereIn( 'post_status_id', $postStatuses )->first();
        if ( !$thePost ) {
            return $this->_not_found();
        }

        //#! Update the frontend locale so the post can be previewed correctly
        VPML::setFrontendLanguageCode( $thePost->language->code );

        //#! Check the post type
        $postType = $thePost->post_type->name;

        $GLOBALS[ 'vp_post' ] = $thePost;

        //#! If this is a page and has specified a template
        if ( 'page' == $postType && ( $template = vp_get_post_meta( $thePost, 'template' ) ) ) {
            return view( $template )->with( [ 'page' => $thePost ] );
        }

        //#! [::1] Check to see whether or not there is a specific template for this post type
        //#! Ex: views/page.blade.php to render all post type page
        if ( view()->exists( $postType ) ) {
            return view( $postType )->with( [ 'page' => $thePost ] );
        }

        //#! [::2] Check to see whether or not there is a specific template for this post type
        //#! Ex: views/singular-article.blade.php to render all post type article
        if ( view()->exists( 'singular-' . $postType ) ) {
            return view( 'singular-' . $postType )->with( [ 'page' => $thePost ] );
        }

        //#! Return the single general template
        return view( 'singular' )->with( [
            'post' => $thePost,
            'settings' => $this->settings,
        ] );
    }

}
