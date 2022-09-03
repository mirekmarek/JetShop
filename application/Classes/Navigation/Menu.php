<?php
namespace JetShop;

use Jet\Data_Tree;
use Jet\Data_Tree_Node;

class Navigation_Menu extends Core_Navigation_Menu {

	protected ?Data_Tree $__tree = null;


	public function generate() : void
	{
		if( str_contains( $this->id, ':' ) ) {
			[$name, $id] = explode(':', $this->id);

			$method = 'generate_'.$name;

			$this->{$method}( $id );

		} else {
			$method = 'generate_'.$this->id;

			$this->{$method}();
		}
	}


	protected function getTree() : Data_Tree
	{
		if(!$this->__tree) {
			$where = [
				Shops::getCurrent()->getWhere('categories_shop_data.'),
				'AND',
				'categories_shop_data.is_active' => true,
				'AND',
				'categories_shop_data.nested_visible_products_count >' => 0
			];

			$data = Category::dataFetchAll(
				select: [
					'id' => 'id',
					'parent_id' => 'parent_id',
					'priority' => 'priority',
					'name' => 'categories_shop_data.name',
					'second_name' => 'categories_shop_data.second_name',
					'is_active' => 'categories_shop_data.is_active',
					'children' => 'categories_shop_data.children',
					'URL_path_part' => 'categories_shop_data.URL_path_part',
					'image_pictogram' => 'categories_shop_data.image_pictogram',
					'image_main' => 'categories_shop_data.image_main',
				],
				where: $where,
				order_by: 'priority'
			);


			$tree = new Data_Tree();
			$tree->getRootNode()->setId(0);
			$tree->getRootNode()->setLabel('');

			$tree->setAdoptOrphans(true);

			$tree->setData( $data );

			$this->__tree = $tree;
		}


		return $this->__tree;
	}

	protected function getNodeAsItem( Data_Tree_Node $node, string $image_key, int $max_w, int $max_h ) : Navigation_Menu_Item|bool
	{
		$d = $node->getData();



		$menu_item = new Navigation_Menu_Item( $this );

		$name = $d['second_name'];
		if(!$name) {
			$name = $d['name'];
		}

		$image = $d[$image_key];

		$menu_item->setId( $node->getId() );
		$menu_item->setLabel( $name );
		$menu_item->setURL( $d['URL_path_part'] );
		$menu_item->setIconURL( Images::getThumbnailUrl( $image, $max_w, $max_h ) );

		return $menu_item;

	}

	public function generate_topMenu() : void
	{
		$tree = $this->getTree();

		foreach( $tree as $node ) {
			if(!$node->getId()) {
				continue;
			}
			if($node->getDepth()>1) {
				continue;
			}

			$d = $node->getData();
			if(
				!$d['children']
			) {
				continue;
			}

			$menu_item = $this->getNodeAsItem($node, 'image_pictogram', 50, 50);
			if(!$menu_item) {
				continue;
			}

			$this->items[] = $menu_item;

			$children = explode(',', $d['children']);

			foreach($children as $id) {
				$s_node = $tree->getNode( $id );
				if(!$s_node) {
					continue;
				}

				$sub_item = $this->getNodeAsItem($s_node, 'image_pictogram', 50, 50);
				if(!$sub_item) {
					continue;
				}


				$menu_item->appendChild( $sub_item );

				$this->items[] = $sub_item;

			}
		}
	}

	public function generate_homepage() : void
	{
		$tree = $this->getTree();

		foreach( $tree as $node ) {
			if(!$node->getId()) {
				continue;
			}
			if($node->getDepth()>1) {
				continue;
			}
			$d = $node->getData();
			if(
				!$d['children']
			) {
				continue;
			}

			$menu_item = $this->getNodeAsItem($node, 'image_pictogram', 50, 50);
			if(!$menu_item) {
				continue;
			}

			$this->items[] = $menu_item;

			$children = explode(',', $d['children']);

			$c = 0;
			foreach($children as $id) {
				$s_node = $tree->getNode( $id );
				if(!$s_node) {
					continue;
				}

				$sub_item = $this->getNodeAsItem($s_node, 'image_pictogram', 50, 50);
				if(!$sub_item) {
					continue;
				}


				$menu_item->appendChild( $sub_item );

				$this->items[] = $sub_item;

				$c++;

				if($c==3) {
					break;
				}
			}
		}
	}

	public function generate_top_category( int $id ) : void
	{
		$tree = $this->getTree();

		$root_node = $tree->getNode($id);

		if(!$root_node) {
			return;
		}

		foreach($root_node->getChildren() as $node) {

			$menu_item = $this->getNodeAsItem($node, 'image_main', 100, 100);
			if(!$menu_item) {
				continue;
			}

			$this->items[] = $menu_item;

			$d = $node->getData();

			$children = explode(',', $d['children']);

			$c = 0;
			foreach($children as $id) {
				$s_node = $tree->getNode( $id );
				if(!$s_node) {
					continue;
				}

				$sub_item = $this->getNodeAsItem($s_node, 'image_pictogram', 50, 50);
				if(!$sub_item) {
					continue;
				}


				$menu_item->appendChild( $sub_item );

				$this->items[] = $sub_item;

				$c++;

				if($c==3) {
					break;
				}
			}

		}
	}

	public function generate_subcategories( int $id ) : void
	{
		$tree = $this->getTree();

		$root_node = $tree->getNode($id);

		if(!$root_node) {
			return;
		}

		foreach($root_node->getChildren() as $node) {

			$menu_item = $this->getNodeAsItem($node, 'image_main', 100, 100);
			if(!$menu_item) {
				continue;
			}

			$this->items[] = $menu_item;

		}

	}
}