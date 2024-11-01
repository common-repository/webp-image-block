<?php 
namespace KK_EL_WIDGETS\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use WebpConverters;

if(!defined('ABSPATH')) exit;

class Webpimage extends Widget_Base{

    public function get_name(){
        return 'webpimage';
    }

    public function get_title(){
        return 'Webp Image';
    }

    public function get_icon(){
        return 'fa fa-image';
    }

    public function get_categories(){
        return ['general'];
    }



    protected function _register_controls(){

        $this->start_controls_section(
            'section_content',
            ['label' => 'Settings']
        );

        $this->add_control(
            'image',
            [
                'label' => 'Choose Image',
                'type'  => \Elementor\Controls_Manager::MEDIA,
                'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				]
            ]
        );

       

        $this->add_group_control(
			\Elementor\Group_Control_Image_Size::get_type(),
			[
				'name' => 'image',
				'default' => 'large',
				'separator' => 'none',
			]
        );
        
        $this->add_control(
			'text_align',
			[
				'label' => __( 'Alignment', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'plugin-domain' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'plugin-domain' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'plugin-domain' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'toggle' => true,
			]
		);

        $this->add_control(
            'link',
            [
                'label' => 'Link',
                'type'  => \Elementor\Controls_Manager::URL,
            ]
        );

        $this->end_controls_section();
    }


    //PHP Render
    protected function render(){

        $settings = $this->get_settings_for_display();
     
        $webpImage = get_post_meta($settings['image']['id'], '_webp_image_'.$settings['image_size'],true);
        if(!$webpImage || $webpImage == ''){
            $imgMeta = get_post_meta($settings['image']['id'], '_wp_attachment_metadata',true);
            $img = wp_get_attachment_image_src($settings['image']['id'], $settings['image_size']);
            $imgPath = parse_url($img[0])['path'];
        
            
            $imagickConverter = new WebpConverters\ImagickConverter();
            $response = $imagickConverter->convertToWebp($imgPath);  
            if(!$response['success']){
                $GDConverter = new WebpConverters\GDConverter();
                $response = $GDConverter->convertImage(rtrim(ABSPATH, '/').$imgPath, 90);
            }
            if($response['success']){
                update_post_meta($settings['image']['id'], '_webp_image_'.$settings['image_size'], $response['data']);

                $webpImage = $response['data'];
            }else{
                $webpImage = $img[0];
                echo $response['message'];
            }

            echo $this->render_image_block($webpImage);

        }else{
            if(strpos($_SERVER['HTTP_ACCEPT'], 'webp')){
                echo $this->render_image_block($webpImage);
            }else{
                $img = wp_get_attachment_image_src($settings['image']['id'], $settings['image_size']);
                echo $this->render_image_block($img[0]);
            }
        }
      

    }

    //JS render
    protected function _content_template(){
        ?>
        <#
		var image = {
			id: settings.image.id,
			url: settings.image.url,
			size: settings.image_size,
			dimension: settings.image_custom_dimension,
			model: view.getEditModel()
		};

		var image_url = elementor.imagesManager.getImageUrl( image );

		if ( ! image_url ) {
			return;
		}
		#>
        <div style="text-align: {{{ settings.text_align }}}">
		    <img src="{{{ image_url }}}" />
        </div>

        <?php 
    }


    /**
     * 
     */
    protected function render_image_block($img){
        $settings = $this->get_settings_for_display();
        $link = $settings['link'];

        $nofollow = $link['nofollow'] == 'on' ? 'rel="nofollow"' : '';
        $target = $link['is_external'] == 'on' ? 'target="_blank"' : '';

        $custom_attributes = '';
        if($link['custom_attributes'] && $link['custom_attributes'] != ''){
            $attributes = explode(',', $link['custom_attributes']);
            
            foreach($attributes as $att){
                $keyVal = explode('|',$att);
                if(count($keyVal) == 2){
                    $custom_attributes .= "{$keyVal[0]}=\"{$keyVal[1]}\" ";
                }

            }
        }


        $output = '';
        $output .= '<div class="el-webp-image" style="text-align:'.$settings['text_align'].'" >';
            if($link['url'] != '')
                $output .= '<a href="'.$link['url'].'" '.$nofollow.' '.$target.' '.$custom_attributes.'>';

                $output .=  '<img src="'.$img.'" loading="lazy">';

            if($link['url'] != '')
                $output .= '</a>';
        $output .= '</div>';

        return $output;
    }

    
}