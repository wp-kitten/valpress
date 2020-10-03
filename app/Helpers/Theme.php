<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class Theme
{
    /**
     * Stores the system path to the themes directory
     * @var string
     */
    private $themesDir = '';

    /**
     * Stores the name of the theme's directory
     * @var string
     */
    private $_dirName;

    /**
     * Stores the system path to the theme's directory
     * @var string
     */
    private $_dirPath;

    /**
     * Stores the http path to the theme's directory
     * @var string
     */
    private $themeUrl = '';

    /**
     * Stores the errors generated by this class
     * @var array
     */
    private $_errors = [];

    /**
     * Stores the data from theme's theme.json file
     * @var array
     */
    private $_data = [];

    /**
     * Theme constructor.
     * @param string $themeDirName
     */
    public function __construct( string $themeDirName )
    {
        $this->themesDir = untrailingslashit( wp_normalize_path( public_path( 'themes' ) ) );
        $this->_dirName = $themeDirName;
        $this->_dirPath = path_combine( $this->themesDir, $themeDirName );
        $this->themeUrl = url( str_ireplace( trailingslashit( wp_normalize_path( public_path() ) ), '', $this->_dirPath ) );
    }

    /**
     * Retrieve the theme info stored in the theme.json file
     * @return array
     */
    public function getThemeData()
    {
        $themeInfoFileData = json_decode( File::get( path_combine( $this->_dirPath, 'theme.json' ) ), true );
        if ( !is_array( $themeInfoFileData ) || !isset( $themeInfoFileData[ 'name' ] ) ) {
            return [];
        }
        $this->_data = $themeInfoFileData;
        return $themeInfoFileData;
    }

    /**
     * Check to see whether the theme is valid
     * @return bool
     */
    public function isValid()
    {
        $errors = $this->__checkTheme();
        return empty( $errors );
    }

    /**
     * Check to see whether the current theme is a child theme
     * @return bool
     */
    public function isChildTheme()
    {
        return ( !empty( $this->get( 'extends', '' ) ) );
    }

    /**
     * Retrieve the reference to the instance of the parent theme
     * @return Theme|null
     */
    public function getParentTheme(): ?Theme
    {
        if ( !$this->isChildTheme() ) {
            return null;
        }
        $parent = $this->get( 'extends', '' );
        $parentTheme = new Theme( $parent );
        if ( !$parentTheme->isValid() ) {
            $this->_errors = array_merge( $this->_errors, $parentTheme->getErrors() );
            return null;
        }
        return $parentTheme;
    }

    /**
     * Load theme's default files
     *
     * @uses do_action( 'contentpress/theme/activated', $this );
     */
    public function load()
    {
        //#! Load the parent theme first
        $parentTheme = $this->getParentTheme();
        if ( $parentTheme ) {
            //#! Load parent theme's files
            $functionsFile = path_combine( $parentTheme->getDirPath(), 'functions.php' );
            if ( File::isFile( $functionsFile ) ) {
                require_once( $functionsFile );
            }
        }

        //#! Load theme's files
        $functionsFile = path_combine( $this->_dirPath, 'functions.php' );
        if ( File::isFile( $functionsFile ) ) {
            require_once( $functionsFile );
        }

        do_action( 'contentpress/theme/activated', $this );
    }

    /**
     * Retrieve the errors generated during this class's execution
     * @return array
     */
    public function getErrors(): array
    {
        return $this->_errors;
    }

    /**
     * Retrieve tan entry from the theme's theme.json file
     * @param string $key
     * @param false $default
     * @return false|mixed
     */
    public function get( string $key, $default = false )
    {
        if ( empty( $this->_data ) ) {
            return $default;
        }
        return ( isset( $this->_data[ $key ] ) ? $this->_data[ $key ] : $default );
    }

    /**
     * Check to see whether the parent theme directory exists
     * @return bool
     */
    public function parentThemeDirExists()
    {
        $parent = $this->getParentTheme();
        if ( !$parent ) {
            return false;
        }
        return ( File::isDirectory( $parent->getDirPath() ) );
    }

    /**
     * Retrieve the theme's directory name
     * @return string
     */
    public function getDirName(): string
    {
        return $this->_dirName;
    }

    /**
     * Retrieve the theme's directory path
     * @return string
     */
    public function getDirPath(): string
    {
        return $this->_dirPath;
    }

    /**
     * Retrieve the http path to the specified resource
     * @param string $resource
     * @return string
     */
    public function url( string $resource ): string
    {
        return path_combine( $this->themeUrl, $resource );
    }

    /**
     * Check to see whether or not the specified theme has a valid file structure
     * @return array
     */
    private function __checkTheme(): array
    {
        $themeDirName = basename( $this->_dirPath );
        $themeInfoFile = path_combine( $this->_dirPath, 'theme.json' );
        $themeFunctionsFile = path_combine( $this->_dirPath, 'functions.php' );
        if ( !File::isFile( $themeInfoFile ) ) {
            $this->_errors[] = __( 'a.The theme :name is not valid, the :file is missing.', [
                'name' => $themeDirName,
                'file' => $themeInfoFile,
            ] );
        }
        else {
            //! Ensure the theme.json file is valid
            $themeInfoFileData = $this->getThemeData();
            if ( empty( $themeInfoFileData ) ) {
                $this->_errors[] = __( 'a.The theme :name is not valid, the :file is not properly formatted or it misses entries.', [
                    'name' => $themeDirName,
                    'file' => $themeInfoFile,
                ] );
            }
            //#! If this is a child theme, make sure it doesn't extend a child theme
            elseif($parentTheme = $this->getParentTheme()) {
                if ( $parentTheme && $parentTheme->isChildTheme() ) {
                    $this->_errors[] = __( 'a.Invalid theme: :name must not extend another child theme: :theme', [
                        'name' => $themeInfoFileData[ 'display_name' ],
                        'theme' => $parentTheme->get( 'display_name' ),
                    ] );
                }
            }
        }

        if ( !File::isFile( $themeFunctionsFile ) ) {
            $this->_errors[] = __( 'a.The theme :name is not valid, the :file is missing.', [
                'name' => $themeDirName,
                'file' => $themeFunctionsFile,
            ] );
        }
        return $this->_errors;
    }

}
