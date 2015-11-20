<?php

/**
 * Instant Articles DOM Tranformation Filter
 *
 * @since 0.1
 */
abstract class Instant_Articles_DOM_Transform_Filter {


	protected $_DOMDocument;
	protected $_post_id;

	/**
	 * Constructor
	 *
	 * @since 0.1
	 * @param DOMDocument  $DOMDocument  The DOMDocument object we will be working on.
	 * @param int          $post_id      The WP post ID to the current post
	 */
	function __construct( $DOMDocument, $post_id ) {
		$this->_DOMDocument = $DOMDocument;
		$this->_post_id = $post_id;
	}

	/**
	 * Run the transformation
	 *
	 * @since 0.1
	 * @return DOMDocument  THe transformed DOM document.
	 */
	abstract public function run();

	/**
	 * Dispach each element in the nodelist to the transformer
	 *
	 * Note that we work directly on the DOMNodeList itself. Objects are passed by ref.
	 *
	 * @since 0.1
	 * @param DOMNodeList  $DOMNodeList  List of images
	 * @return DOMNodeList  The DOMNodeList. If you want to chain.
	 */
	protected function _transform_elements( DOMNodeList $DOMNodeList ) {

		// A foreach won’t work as we are changing the elements
		for ( $i = 0, $c = $DOMNodeList->length; $i < $c; ++$i ) {
			$this->_transform_element( $DOMNodeList->item( $i ) );
		}

		return $DOMNodeList;
	}

	/**
	 * Transform an element
	 *
	 * Note that we work directly on the DOMNode itself. Objects are passed by ref.
	 *
	 * @since 0.1
	 * @param DOMNode  $DOMNode  The original DOM node
	 * @return DOMNode  The tranformed DOMNode. If you want to chain.
	 */
	protected function _transform_element( DOMNode $DOMNode ) {
		
		$src = $DOMNode->getAttribute( 'src' );

		// See how far up the tree we can go.
		$replaceNode = $DOMNode;
		while ( 'body' != $replaceNode->parentNode->nodeName && 1 == $replaceNode->parentNode->childNodes->length ) {
			$replaceNode = $replaceNode->parentNode;
		}
		// If we can’t go all the way to the top, we bail.
		if ( 'body' != $replaceNode->parentNode->nodeName ) {
			return $DOMNode;
		}


		$properties = $this->get_properties( $DOMNode );
		$DOMDocumentFragment = $this->_build_fragment( $properties );

		if ( is_a( $DOMDocumentFragment, 'DOMDocumentFragment' ) ) {
			$replaceNode->parentNode->replaceChild( $DOMDocumentFragment, $replaceNode );
		}

		return $DOMNode;
	}

	/**
	 * Find the element properties
	 *
	 * @since 0.1
	 * @param $DOMNode  $DOMNode        The original domnode
	 */
	abstract protected function get_properties( $DOMNode );

}

