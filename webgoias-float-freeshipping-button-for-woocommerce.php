<?php
/**
 * Plugin Name: Webgoias - Float Freeshipping Button for Woocommerce
 * Plugin URI: https://www.webgoias.com.br/
 * Description: Tenha um botao flutuante para avisar o valor que falta para ganhar frete grátis em seu Woocommerce
 * Version: 1.5.1
 * Text Domain: wbg-freeshiping-float-button
 * Domain Path: /languages
 * License: GPLv3 or later
 * Author: Rodrigo Fleury Bastos - Webgoias
 * Author URI: http://www.webgoias.com.br
 */


if (! defined('ABSPATH'))
{
    exit;
}


if ( !function_exists( 'deactivate_plugins' ) ) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}

define('WBG_FREESHIPPING_FLOAT_BUTTON_PATH', plugin_dir_path(__FILE__));
define('WBG_FREESHIPPING_FLOAT_BUTTON_URL', plugin_dir_url(__FILE__));
define('WBG_FREESHIPPING_FLOAT_BUTTON_URL_IMAGES', plugin_dir_url(__FILE__)."images");
define('WBG_FREESHIPPING_FLOAT_BUTTON_PATH_IMAGES', plugin_dir_url(__FILE__)."images");
define('WBG_FREESHIPPING_FLOAT_BUTTON_VERSION', '1.5.0');
define('WBG_FREESHIPPING_FLOAT_BUTTON_SLUG', 'wbg_freeshipping_float_button');



//validando PHP 7.2
if ( version_compare( PHP_VERSION, '7.2.0', '>=' ) ) {
    //Validando mbstring
    $user_id = get_current_user_id();
    if(extension_loaded('mbstring')!=1) {
        add_action( 'admin_notices', 'wbg_mbstring_validate_false' );
        deactivate_plugins( '/webgoias-float-freeshipping-button-for-woocommerce/webgoias-float-freeshipping-button-for-woocommerce.php' );
    }
} else {
    add_action( 'admin_notices', 'wbg_php_version_validate' );
    deactivate_plugins( '/webgoias-float-freeshipping-button-for-woocommerce/webgoias-float-freeshipping-button-for-woocommerce.php' );

}
function wbg_freeshipping_floatbutton_notice_dismiss() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['wbg_freeshipping_floatbutton_dismissed'] ) ) {
        add_user_meta( $user_id, 'wbg_freeshipping_notice_dismiss', 'true', true );
    }
}
add_action( 'admin_init', 'wbg_notice_dismiss' );

function wbg_freeshipping_floatbutton_mbstring_validate_false (){
    ?>
    <div class="error notice is-dismissible">
        <p><?php _e( 'A extensão MBSTRING é necessária para o funcionamento do plugin!', 'wbg-buzzlead-tracker' ); ?></p>
    </div>
    <?php
}

function wbg_freeshipping_floatbutton_php_version_validate (){
    ?>
    <div class="error notice is-dismissible">
        <p><?php _e( 'Por motivos de segurança a versão do PHP mínima requerida é 7.2!', 'wbg-buzzlead-tracker' ); ?></p>
    </div>
    <?php
}


use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('admin_head', 'wbg_custom_css_adm');
function wbg_custom_css_adm() {
  echo '<style>.pix-donate {background: lightskyblue;} .titulo-separator {background: beige;}</style>';
}

add_action( 'carbon_fields_register_fields', 'wbg_freeshipping_floatbutton_theme_options' );
function wbg_freeshipping_floatbutton_theme_options() {
    $html = 'Realize a configuração abaixo, preenchendo o valor minimo para o frete gratis (você deverá configurar isto previamente no seu Woocommerce, criando o frete grátis nas áreas de entrega, este plugin apenas gera o botão flutuante.) <br/> Caso não faça nenhuma alteração o botão ficará padrão (Mensagem,Cores, posicionamento e icone)<br/><br/>';
    $htmlpix = '<b>FAÇA UMA DOAÇÃO para meu PIX e me ajude a manter este e outros modulos</b> <BR/> <img width="180" src="data:image/png;base64,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"><br/><b>CHAVE:</b> feff4dc0-51dc-4dcf-97e0-f66afcb34ee0 <br/><b>Beneficiario:</b> Rodrigo Fleury Bastos<br/><b>Banco:</b> NuBank';
    Container::make( 'theme_options', __( 'Float Freeshipping Button', 'crb' ) )
        ->set_icon( WBG_FREESHIPPING_FLOAT_BUTTON_URL_IMAGES.'/icone.png' )
        ->add_fields( array(
            Field::make( 'html', 'crb_information_text' )->set_html( '<center><img src="'.WBG_FREESHIPPING_FLOAT_BUTTON_URL_IMAGES.'/logo.png'.'"></center><br/><h1>WebGoias - Botão Flutuante para Frete Grátis</h2><p>'.$html.'</p>' ),
            Field::make( 'separator', 'crb_separator_pix', __( 'Faça uma doação!' ) )->set_classes( 'pix-donate' ),
            Field::make( 'html', 'crb_information_text_pix' )->set_html( '<p>'.$htmlpix.'</p>' ),
            Field::make( 'separator', 'crb_separator_configs', __( 'Configurações do Módulo' ) )->set_classes( 'titulo-separator' ),
            Field::make( 'text', 'wbg_freeshipping_valorminimo', 'Valor Mínimo para Frete Gratis' )->set_width( 50 )->set_required( true )->set_help_text( "Valor minimo para ganhar frete grátis. Lembrando que você precisa criar essa regra primeiramente nos métodos de entrega" ),
            Field::make( 'text', 'wbg_freeshipping_textomsg', 'Texto da mensagem (HTML é permitido)' )->set_required( false )->set_attribute( 'placeholder', 'Utilize %valor% onde desejar usar o valor restante.' )->set_help_text( "Escolha a mensagem desejada adicionando a variável %valor% (Por ex: Com apenas %valor% você ganha <b>FRETE GRÁTIS</b>) onde deseja que apareça o valor faltando para para atingir o Mínimo. Caso não preecher este campo será exibida uma mensagem padrão" ),

            Field::make( 'separator', 'crb_separator', __( 'Ajustes de Posição e Cor ' ) )->set_classes( 'titulo-separator' ),

            Field::make( 'radio', 'wbg_radio_posicao', __( 'Escolha a Posição' ) )
                ->set_options( array(
                    'left' => "Esquerda",
                    'right' => "Direita"
                ) )->set_help_text( "Escolha de qual lado você deseja que o botão flutuante apareça" ),
            Field::make( 'text', 'wbg_freeshipping_altura', 'Altura em Pixel' )->set_attribute( 'placeholder', '45px' )->set_help_text( "Defina a altura (contando a partir do rodapé) em pixels que irá aparecer o botão. Valor inicial padrão é de 45px" ),
            Field::make( 'text', 'wbg_freeshipping_lateral', 'Espaço Lateral em Pixel' )->set_attribute( 'placeholder', '20px' )->set_help_text( "Defina o espaço lateral em pixels que irá aparecer o botão. Valor inicial padrão é de 20px" ),
            Field::make( 'color', 'wbg_freeshipping_cor_progress', __( 'Cor barra de Progresso' ) )->set_help_text( "Defina a cor da barrinha de progresso" ),
            Field::make( 'color', 'wbg_freeshipping_cor_font_progress', __( 'Cor Fonte barra de Progresso' ) )->set_help_text( "Defina a cor do texto na barrinha de progresso" ),
            Field::make( 'color', 'wbg_freeshipping_cor_borda', __( 'Cor barra Borda' ) )->set_help_text( "Defina a cor da borda" ),
            Field::make( 'image', 'wbg_freeshipping_icone', __( 'Icone' ) )->set_value_type( 'url' )->set_help_text( "Escolha um icone. Preferencialmente redondo 90x90px com fundo (com ou sem cor). Caso o icone não seja redondo e seja sem fundo, existirá um ajuste de CSS." ),
            Field::make( 'textarea', 'wbg_freeshipping_css_adicional', __( 'CSS Adicional' ) )->set_help_text( 'Adicone mais CSS para ajustar seu layout. Este CSS é executado após o CSS padrão. Exige um conhecimento básico para isto.' )
                ->set_rows( 4 )
        ) );
}

add_action( 'after_setup_theme', 'wbg_freeshipping_load' );
function wbg_freeshipping_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
}

########## WOOCOMMERCE #############
add_action( 'carbon_fields_fields_registered', 'wbg_freeshipping_value_avail' );
function wbg_freeshipping_value_avail() {

    //Se valor minimo estiver preenchido
    if(carbon_get_theme_option( 'wbg_freeshipping_valorminimo' ) != "") {


        function addFloatFreteGratis() {

            global $woocommerce;
            $totalCarrinho = WC()->cart->get_cart_contents_total();

            $valorminimo = carbon_get_theme_option( 'wbg_freeshipping_valorminimo' );
            $textomsg = carbon_get_theme_option( 'wbg_freeshipping_textomsg' );
            $corProgresso = carbon_get_theme_option( 'wbg_freeshipping_cor_progress' );
            $corProgressoFont = carbon_get_theme_option( 'wbg_freeshipping_cor_font_progress' );
            $corBorda = carbon_get_theme_option( 'wbg_freeshipping_cor_borda' );
            $icone = esc_url_raw(carbon_get_theme_option( 'wbg_freeshipping_icone' ));
            $posicao = carbon_get_theme_option( 'wbg_radio_posicao');
            $altura = carbon_get_theme_option( 'wbg_freeshipping_altura');
            $lateral = carbon_get_theme_option( 'wbg_freeshipping_lateral');
            $cssAdicional = carbon_get_theme_option( 'wbg_freeshipping_css_adicional' );

            #die($valorminimo);

            $diferenca = $valorminimo - $totalCarrinho;
            $percentual = ($totalCarrinho*100)/$valorminimo;


            $iconePadrao = "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIHdpZHRoPSI3OXB4IiBoZWlnaHQ9IjgxcHgiIHZpZXdCb3g9IjAgMCA3OSA4MSIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4gICAgICAgIDx0aXRsZT5Hcm91cCA2PC90aXRsZT4gICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+ICAgIDxnIGlkPSJIb21lIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4gICAgICAgIDxnIGlkPSJob3Zlci1mcmV0ZSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTE1NzMuMDAwMDAwLCAtNjIzLjAwMDAwMCkiPiAgICAgICAgICAgIDxnIGlkPSJHcm91cCIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTQxNy4wMDAwMDAsIDYyMy4wMDAwMDApIj4gICAgICAgICAgICAgICAgPGcgaWQ9Ikdyb3VwLTIiPiAgICAgICAgICAgICAgICAgICAgPGcgaWQ9Ikdyb3VwLTUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDE1Ni4wMDAwMDAsIDAuMDAwMDAwKSI+ICAgICAgICAgICAgICAgICAgICAgICAgPGcgaWQ9Ik92YWwtMiI+ICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxnIGlkPSJHcm91cC02Ij4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxlbGxpcHNlIGlkPSJPdmFsIiBmaWxsPSIjNkJENEZDIiBjeD0iMzkuNSIgY3k9IjQzIiByeD0iMzkuNSIgcnk9IjM4Ij48L2VsbGlwc2U+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZyBpZD0ic21va2UiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDguMDAwMDAwLCAwLjAwMDAwMCkiPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxnIGlkPSJQYXRoIj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGc+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjIuNzI3MjcyNywxMC43MjQzNzQxIEwyMi43MjcyNzI3LDEwLjU1NTU1NTYgQzIyLjcyNzI3MjcsOC45Mjk5Mjk2MyAyMS43MDQ1NDU1LDcuNDk0NDQ0NDQgMjAuMjA0NTQ1NSw2Ljc5NzcwNzQxIEMyMC4zNjM2MzY0LDYuMjkxMDQwNzQgMjAuNDU0NTQ1NSw1Ljc4NDQ0NDQ0IDIwLjQ1NDU0NTUsNS4yNzc3Nzc3OCBDMjAuNDU0NTQ1NSwyLjM2NDQ0NDQ0IDE3LjkwOTA5MDksMCAxNC43NzI3MjczLDAgTDUuNjgxODE4MTgsMCBDMi41NDU0NTQ1NSwwIDAsMi4zNjQ0NDQ0NCAwLDUuMjc3Nzc3NzggQzAsOC4xOTExMTExMSAyLjU0NTQ1NDU1LDEwLjU1NTU1NTYgNS42ODE4MTgxOCwxMC41NTU1NTU2IEw2LjgxODE4MTgyLDEwLjU1NTU1NTYgQzYuODE4MTgxODIsMTIuODc3NzA3NCA4Ljg2MzcxMjEyLDE0Ljc3Nzc3NzggMTEuMzYzNjM2NCwxNC43Nzc3Nzc4IEwxMy44NDA5ODQ4LDE0Ljc3Nzc3NzggQzE0LjI5NTQ1NDUsMTYuMDAyMTUxOSAxNS41NjgxMDYxLDE2Ljg4ODg4ODkgMTcuMDQ1NDU0NSwxNi44ODg4ODg5IEwyMS4xMTM1NjA2LDE2Ljg4ODg4ODkgTDIzLjA2ODE4MTgsMTguNjgzMjYzIEMyMy4yNzI3MjczLDE4Ljg5NDQ0NDQgMjMuNTY4MTgxOCwxOSAyMy44NjM2MzY0LDE5IEMyNCwxOSAyNC4xNTkwOTA5LDE4Ljk3ODg4ODkgMjQuMjk1NDU0NSwxOC45MTU0ODUyIEMyNC43MjcyNzI3LDE4Ljc0NjY2NjcgMjUsMTguMzY2NjY2NyAyNSwxNy45NDQ0NDQ0IEwyNSwxMy43MjIyMjIyIEMyNSwxMi4zNDk5Mjk2IDI0LjA0NTUzMDMsMTEuMTY3NzA3NCAyMi43MjcyNzI3LDEwLjcyNDM3NDEgWiIgZmlsbD0iIzhGOTdBRSIgZmlsbC1ydWxlPSJub256ZXJvIj48L3BhdGg+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMjUsMTMuNzIyMjIyMiBMMjUsMTcuOTQ0NDQ0NCBDMjUsMTguMzY2NjY2NyAyNC43MjcyNzI3LDE4Ljc0NjY2NjcgMjQuMjk1NDU0NSwxOC45MTU0ODUyIEMyNC4xNTkwOTA5LDE4Ljk3ODg4ODkgMjQsMTkgMjMuODYzNjM2NCwxOSBDMjMuNTY4MTgxOCwxOSAyMy4yNzI3MjczLDE4Ljg5NDQ0NDQgMjMuMDY4MjU3NiwxOC42ODMyNjMgTDIxLjExMzU2MDYsMTYuODg4ODg4OSBMMTcuMDQ1NDU0NSwxNi44ODg4ODg5IEMxNS41NjgxMDYxLDE2Ljg4ODg4ODkgMTQuMjk1Mzc4OCwxNi4wMDIxNTE5IDEzLjg0MDk4NDgsMTQuNzc3Nzc3OCBMMTIuNSwxNC43Nzc3Nzc4IEwxMi41LDAgTDE0Ljc3MjcyNzMsMCBDMTcuOTA5MDkwOSwwIDIwLjQ1NDU0NTUsMi4zNjQ0NDQ0NCAyMC40NTQ1NDU1LDUuMjc3Nzc3NzggQzIwLjQ1NDU0NTUsNS43ODQ0NDQ0NCAyMC4zNjM3MTIxLDYuMjkxMDQwNzQgMjAuMjA0NTQ1NSw2Ljc5NzcwNzQxIEMyMS43MDQ1NDU1LDcuNDk0NDQ0NDQgMjIuNzI3MjcyNyw4LjkyOTkyOTYzIDIyLjcyNzI3MjcsMTAuNTU1NTU1NiBMMjIuNzI3MjcyNywxMC43MjQzNzQxIEMyNC4wNDU1MzAzLDExLjE2NzcwNzQgMjUsMTIuMzQ5OTI5NiAyNSwxMy43MjIyMjIyIFoiIGZpbGw9IiM3QTg2OUEiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2c+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9nPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxnIGlkPSJkZWxpdmVyeSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTguNjU4NTM3LCAxMy4zMTU3ODkpIj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGc+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMzEuOTY0NzE3NiwxOS4wMTcwMzQ1IEwzMS45NjQ3MTc2LDI5LjY0NDIwMDkgTDMwLjMxMzE0OTgsMjkuNjQ0MjAwOSBDMzAuMzEzMTQ5OCwyNy4xNzI4NDk0IDI4LjM0MTQyOTUsMjUuMTY5NjA0NSAyNS45MDg5NjkxLDI1LjE2OTYwNDUgQzIzLjQ3NjUwODgsMjUuMTY5NjA0NSAyMS41MDQ3ODg1LDI3LjE3Mjg0OTQgMjEuNTA0Nzg4NSwyOS42NDQyMDA5IEwxOS44NTMyMjA3LDI5LjY0NDIwMDkgTDE5Ljg1MzIyMDcsMTIuODY0NDY0NSBMMjcuNTkzNjAwNSwxMi44NjQ0NjQ1IEwzMS45NjQ3MTc2LDE5LjAxNzAzNDUgWiIgaWQ9IlBhdGgiIGZpbGw9IiMwMDVFQ0UiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlBhdGgiIGZpbGw9IiM1ODU5NUIiIGZpbGwtcnVsZT0ibm9uemVybyIgcG9pbnRzPSIyMS41MDQ3ODg1IDE0LjU0MjQzODIgMjEuNTA0Nzg4NSAxOS41NzYzNTkxIDMwLjMxMzE0OTggMTkuNTc2MzU5MSAyNi43OTMwODQ3IDE0LjU0MjQzODIiPjwvcG9seWdvbj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwYXRoIGQ9Ik0xOC4yMDE2NTMsMjIuOTMyMzA2MyBMMC4wMzQ0MDc2NjE1LDIyLjkzMjMwNjMgTDAuMDM0NDA3NjYxNSwyOS42NDQyMDA5IEwyLjIzNjQ5OCwyOS42NDQyMDA5IEMyLjIzNjQ5OCwyNy4xNzI4NDk0IDQuMjA4MjE4MzEsMjUuMTY5NjA0NSA2LjY0MDY3ODY4LDI1LjE2OTYwNDUgQzkuMDczMTM5MDQsMjUuMTY5NjA0NSAxMS4wNDQ4NTk0LDI3LjE3Mjg0OTQgMTEuMDQ0ODU5NCwyOS42NDQyMDA5IEwxOS44NTMyMjA3LDI5LjY0NDIwMDkgTDE5Ljg1MzIyMDcsMjIuOTMyMzA2MyBMMTguMjAxNjUzLDIyLjkzMjMwNjMgWiIgaWQ9IlBhdGgiIGZpbGw9IiMwMDZERjAiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTkuOTQzODE0MTgsMjkuNjQ0MjAwOSBDOS45NDM4MTQxOCwzMS40OTc1MDk2IDguNDY0ODIyMzIsMzMuMDAwMTQ4MSA2LjY0MDY3ODY4LDMzLjAwMDE0ODEgQzQuODE2NTM1MDMsMzMuMDAwMTQ4MSAzLjMzNzU0MzE3LDMxLjQ5NzUwOTYgMy4zMzc1NDMxNywyOS42NDQyMDA5IEMzLjMzNzU0MzE3LDI3Ljc5MDg5MjEgNC44MTY1MzUwMywyNi4yODgyNTM2IDYuNjQwNjc4NjgsMjYuMjg4MjUzNiBDOC40NjQ4MjIzMiwyNi4yODgyNTM2IDkuOTQzODE0MTgsMjcuNzkwODkyMSA5Ljk0MzgxNDE4LDI5LjY0NDIwMDkgWiIgaWQ9IlBhdGgiIGZpbGw9IiM1ODU5NUIiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTI5LjIxMjEwNDYsMjkuNjQ0MjAwOSBDMjkuMjEyMTA0NiwzMS40OTc1MDk2IDI3LjczMzExMjgsMzMuMDAwMTQ4MSAyNS45MDg5NjkxLDMzLjAwMDE0ODEgQzI0LjA4NDgyNTUsMzMuMDAwMTQ4MSAyMi42MDU4MzM2LDMxLjQ5NzUwOTYgMjIuNjA1ODMzNiwyOS42NDQyMDA5IEMyMi42MDU4MzM2LDI3Ljc5MDg5MjEgMjQuMDg0ODI1NSwyNi4yODgyNTM2IDI1LjkwODk2OTEsMjYuMjg4MjUzNiBDMjcuNzMzMTEyOCwyNi4yODgyNTM2IDI5LjIxMjEwNDYsMjcuNzkwODkyMSAyOS4yMTIxMDQ2LDI5LjY0NDIwMDkgWiIgaWQ9IlBhdGgiIGZpbGw9IiM1ODU5NUIiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTI3LjAxMDAxNDMsMjkuNjQ0MjAwOSBDMjcuMDEwMDE0MywzMC4yNjE5NzA1IDI2LjUxNzAxNzEsMzAuNzYyODQ5OSAyNS45MDg5NjkxLDMwLjc2Mjg0OTkgQzI1LjMwMDkyMTIsMzAuNzYyODQ5OSAyNC44MDc5MjQsMzAuMjYxOTcwNSAyNC44MDc5MjQsMjkuNjQ0MjAwOSBDMjQuODA3OTI0LDI5LjAyNjQzMTIgMjUuMzAwOTIxMiwyOC41MjU1NTE4IDI1LjkwODk2OTEsMjguNTI1NTUxOCBDMjYuNTE3MDE3MSwyOC41MjU1NTE4IDI3LjAxMDAxNDMsMjkuMDI2NDMxMiAyNy4wMTAwMTQzLDI5LjY0NDIwMDkgWiIgaWQ9IlBhdGgiIGZpbGw9IiNGMUYyRjIiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTcuNzQxNzIzODUsMjkuNjQ0MjAwOSBDNy43NDE3MjM4NSwzMC4yNjE5NzA1IDcuMjQ4NzI2NiwzMC43NjI4NDk5IDYuNjQwNjc4NjgsMzAuNzYyODQ5OSBDNi4wMzI2MzA3NSwzMC43NjI4NDk5IDUuNTM5NjMzNTEsMzAuMjYxOTcwNSA1LjUzOTYzMzUxLDI5LjY0NDIwMDkgQzUuNTM5NjMzNTEsMjkuMDI2NDMxMiA2LjAzMjYzMDc1LDI4LjUyNTU1MTggNi42NDA2Nzg2OCwyOC41MjU1NTE4IEM3LjI0ODcyNjYsMjguNTI1NTUxOCA3Ljc0MTcyMzg1LDI5LjAyNjQzMTIgNy43NDE3MjM4NSwyOS42NDQyMDA5IFoiIGlkPSJQYXRoIiBmaWxsPSIjRjFGMkYyIiBmaWxsLXJ1bGU9Im5vbnplcm8iPjwvcGF0aD4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBmaWxsPSIjRkY5ODExIiBmaWxsLXJ1bGU9Im5vbnplcm8iIHBvaW50cz0iMTUuOTk5NTYyNiA5LjUwODUxNzI2IDAuNTg0OTMwMjQ2IDkuNTA4NTE3MjYgMC41ODQ5MzAyNDYgMjIuOTMyMzA2MyAxOC4yMDE2NTMgMjIuOTMyMzA2MyAxOC4yMDE2NTMgOS41MDg1MTcyNiI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlBhdGgiIGZpbGw9IiNFRDFDMjQiIGZpbGwtcnVsZT0ibm9uemVybyIgcG9pbnRzPSI3Ljc0MTcyMzg1IDEzLjk4MzExMzYgOS4zOTMyOTE2IDEyLjg2NDQ2NDUgMTEuMDQ0ODU5NCAxMy45ODMxMTM2IDExLjA0NDg1OTQgOS41MDg1MTcyNiA3Ljc0MTcyMzg1IDkuNTA4NTE3MjYiPjwvcG9seWdvbj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBmaWxsPSIjRkZCNjU1IiBmaWxsLXJ1bGU9Im5vbnplcm8iIHBvaW50cz0iMi43ODcwMjA1OCAwIDIuNzg3MDIwNTggOS41MDg1MTcyNiAxNS45OTk1NjI2IDkuNTA4NTE3MjYgMTUuOTk5NTYyNiAwIj48L3BvbHlnb24+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cG9seWdvbiBpZD0iUGF0aCIgZmlsbD0iI0VEMUMyNCIgZmlsbC1ydWxlPSJub256ZXJvIiBwb2ludHM9IjkuMzkzMjkxNiAyLjc5NjYyMjcyIDEwLjQ5NDMzNjggMy4zNTU5NDcyNyAxMC40OTQzMzY4IDAgOC4yOTIyNDY0MyAwIDguMjkyMjQ2NDMgMy4zNTU5NDcyNyI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTAuNTg0OTMwMjQ2LDExLjE4NjQ5MDkgTDAuNTg0OTMwMjQ2LDIyLjkzMjMwNjMgTDEzLjI0Njk0OTcsMjIuOTMyMzA2MyBDNi4yNDgyMTYyOSwyMi45MzIzMDYzIDAuNTg0OTMwMjQ2LDE3LjY3ODUzMzggMC41ODQ5MzAyNDYsMTEuMTg2NDkwOSBaIiBpZD0iUGF0aCIgZmlsbD0iI0VFODcwMCIgZmlsbC1ydWxlPSJub256ZXJvIj48L3BhdGg+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMi43ODcwMjA1OCwzLjM1NTk0NzI3IEwyLjc4NzAyMDU4LDkuNTA4NTE3MjYgTDEwLjQ5NDMzNjgsOS41MDg1MTcyNiBDNi4yMzQ1MDY5Nyw5LjUwODUxNzI2IDIuNzg3MDIwNTgsNi43NTY2ODQxOSAyLjc4NzAyMDU4LDMuMzU1OTQ3MjcgWiIgaWQ9IlBhdGgiIGZpbGw9IiNGRkE3MzMiIGZpbGwtcnVsZT0ibm9uemVybyI+PC9wYXRoPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGcgaWQ9Ikdyb3VwIiB0cmFuc2Zvcm09InRyYW5zbGF0ZSgxMC40NTk5MjksIDUuNTkzMjQ1KSIgZmlsbD0iI0YxRjJGMiIgZmlsbC1ydWxlPSJub256ZXJvIj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cG9seWdvbiBpZD0iUGF0aCIgcG9pbnRzPSIxLjEzNTQ1MjgzIDE0LjU0MjQzODIgMi4yMzY0OTggMTQuNTQyNDM4MiAyLjIzNjQ5OCAxNS42NjEwODcyIDEuMTM1NDUyODMgMTUuNjYxMDg3MiI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBwb2ludHM9IjIuNzg3MDIwNTggMTQuNTQyNDM4MiA1LjUzOTYzMzUxIDE0LjU0MjQzODIgNS41Mzk2MzM1MSAxNS42NjEwODcyIDIuNzg3MDIwNTggMTUuNjYxMDg3MiI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBwb2ludHM9IjEuMTM1NDUyODMgMTIuODY0NDY0NSAyLjIzNjQ5OCAxMi44NjQ0NjQ1IDIuMjM2NDk4IDEzLjk4MzExMzYgMS4xMzU0NTI4MyAxMy45ODMxMTM2Ij48L3BvbHlnb24+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlBhdGgiIHBvaW50cz0iMi43ODcwMjA1OCAxMi44NjQ0NjQ1IDUuNTM5NjMzNTEgMTIuODY0NDY0NSA1LjUzOTYzMzUxIDEzLjk4MzExMzYgMi43ODcwMjA1OCAxMy45ODMxMTM2Ij48L3BvbHlnb24+ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHBvbHlnb24gaWQ9IlBhdGgiIHBvaW50cz0iMC4wMzQ0MDc2NjE1IDEuNjc3OTczNjMgMS4xMzU0NTI4MyAxLjY3Nzk3MzYzIDEuMTM1NDUyODMgMi43OTY2MjI3MiAwLjAzNDQwNzY2MTUgMi43OTY2MjI3MiI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBwb2ludHM9IjEuNjg1OTc1NDIgMS42Nzc5NzM2MyA0LjQzODU4ODM0IDEuNjc3OTczNjMgNC40Mzg1ODgzNCAyLjc5NjYyMjcyIDEuNjg1OTc1NDIgMi43OTY2MjI3MiI+PC9wb2x5Z29uPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwb2x5Z29uIGlkPSJQYXRoIiBwb2ludHM9IjAuMDM0NDA3NjYxNSAwIDEuMTM1NDUyODMgMCAxLjEzNTQ1MjgzIDEuMTE4NjQ5MDkgMC4wMzQ0MDc2NjE1IDEuMTE4NjQ5MDkiPjwvcG9seWdvbj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cG9seWdvbiBpZD0iUGF0aCIgcG9pbnRzPSIxLjY4NTk3NTQyIDAgNC40Mzg1ODgzNCAwIDQuNDM4NTg4MzQgMS4xMTg2NDkwOSAxLjY4NTk3NTQyIDEuMTE4NjQ5MDkiPjwvcG9seWdvbj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cGF0aCBkPSJNMTIuNjk2NDI3MSwxNS4xMDE3NjI3IEwxMi4xNDU5MDQ1LDE1LjEwMTc2MjcgQzExLjg0MTg4MDYsMTUuMTAxNzYyNyAxMS41OTUzODE5LDE1LjM1MjIwMjQgMTEuNTk1MzgxOSwxNS42NjEwODcyIEMxMS41OTUzODE5LDE1Ljk2OTk3MjEgMTEuODQxODgwNiwxNi4yMjA0MTE4IDEyLjE0NTkwNDUsMTYuMjIwNDExOCBMMTIuNjk2NDI3MSwxNi4yMjA0MTE4IEMxMy4wMDA0NTExLDE2LjIyMDQxMTggMTMuMjQ2OTQ5NywxNS45Njk5NzIxIDEzLjI0Njk0OTcsMTUuNjYxMDg3MiBDMTMuMjQ2OTQ5NywxNS4zNTIyMDI0IDEzLjAwMDQ1MTEsMTUuMTAxNzYyNyAxMi42OTY0MjcxLDE1LjEwMTc2MjcgWiIgaWQ9IlBhdGgiPjwvcGF0aD4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZz4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9nPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZz4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZz4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDx0ZXh0IGlkPSJGUkVURS1HUsOBVElTIiBmb250LWZhbWlseT0iTXVsaS1FeHRyYUJvbGQsIE11bGkiIGZvbnQtc2l6ZT0iOSIgZm9udC13ZWlnaHQ9IjYwMCIgbGV0dGVyLXNwYWNpbmc9Ii0wLjE0MTQyODYxIiBmaWxsPSIjRkZGRkZGIj4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8dHNwYW4geD0iOC45MTIwNzE2NiIgeT0iNTYiPkZSRVRFIEdSw4FUSVM8L3RzcGFuPiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC90ZXh0PiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2c+ICAgICAgICAgICAgICAgICAgICAgICAgPC9nPiAgICAgICAgICAgICAgICAgICAgPC9nPiAgICAgICAgICAgICAgICA8L2c+ICAgICAgICAgICAgPC9nPiAgICAgICAgPC9nPiAgICA8L2c+PC9zdmc+";

            if($icone=="") {
                $iconShow = $iconePadrao;
                $paddingPadrao = "0px";
            } else {
                $iconShow = $icone;
                $paddingPadrao = "10px";

            }

            if($corBorda=="") {
                $corBordaShow = "#6bd4fc";
                if($icone!="") {
                    $bordaShow = "border: 2px solid #6bd4fc";
                } else {
                    $bordaShow = "border: none";
                }

            } else {
                $corBordaShow = $corBorda;
                $bordaShow = "border: 2px solid ".$corBorda;

            }

            if($corProgresso=="") {
                $corProgressoShow = "#6bd4fc";
            } else {
                $corProgressoShow = $corProgresso;
            }

            if($corProgressoFont=="") {
                $corProgressoFontShow = "#FFFFFF";
            } else {
                $corProgressoFontShow = $corProgressoFont;
            }

            if($altura == "") {
                $alturaPadrao = "45px";
            } else {
                $alturaPadrao = $altura."px";
            }

            if($lateral == "") {
                $lateralPadrao = "20px";
            } else {
                $lateralPadrao = $lateral."px";
            }

            if($posicao == "") {
                $posicaoPadrao = "right";
            } else {
                $posicaoPadrao = $posicao;
            }

            if($textomsg=="") {
                $msgFrete = 'Faltam <span>R$ '.number_format($diferenca, 2).'</span> para </p><p><strong>ganhar frete grátis!</strong>';
            } else {
                $msgFrete =  str_replace("%valor%","R$ ".number_format($diferenca, 2),$textomsg);;
            }


            if($diferenca > 0) { 
                ?>
                <style>#root-shipping{font-family:'Muli',sans-serif;position:fixed;bottom:<?php echo esc_attr($alturaPadrao);?>;<?php echo esc_attr($posicaoPadrao);?>:<?php echo esc_attr($lateralPadrao);?>;width:230px;overflow:hidden}#root-shipping .shipping-icon img {content:'';position:relative;width:80px;height:85px;background-position:center;background-size:80px;background-repeat:no-repeat;z-index:3;float:right; background:#FFF;<?php echo esc_attr($bordaShow);?> ;border-radius: 50%;padding: <?php echo esc_attr($paddingPadrao); ?>;}#root-shipping .progressFreeShipping__wrapper p{font-size:11px;font-weight:700;color:#393939;display:block;margin:0;padding:0;text-align:center}#root-shipping .progressFreeShipping__wrapper{position:absolute;top:12px;opacity:1;visibility:visible;width:200px;border:2px solid <?php echo esc_attr($corBordaShow);?>;padding:10px 45px 6px 0;background-color:#fff;border-radius:5px;z-index:0;-webkit-transition:.3s;transition:.3s;z-index:1}#root-shipping .progressFreeShipping__wrapper .Progressbar{display:block;width:100%;height:12px;border-radius:5px;background-color:#e6e6e6;margin-left:15px;max-width:122px;margin-top:5px}#root-shipping .progressFreeShipping__wrapper .Progressbar__fill{height:12px;background-color:<?php echo esc_attr($corProgressoShow);?>;border-radius:5px;color:<?php echo esc_attr($corProgressoFontShow);?>;font-size: 10px;text-align: center;}
                    <?php echo esc_attr($cssAdicional); ?>
                </style>
                <div id="root-shipping">
                    <div class="shipping-icon"><img src="<?php echo esc_attr($iconShow); ?>" /></div>

                    <div class="progressFreeShipping__wrapper"></div>

                    <div class="progressFreeShipping__wrapper">
                        <p><?php echo wp_kses_post($msgFrete);  ?></p>
                        <div class="Progressbar">
                            <div class="Progressbar__fill" style="width: <?php echo esc_attr($percentual); ?>%;">
                                <span class="icon-truck"><?php echo esc_attr($percentual); ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        }

        add_action( 'wp_footer' , 'addFloatFreteGratis' );
    }
}

?>