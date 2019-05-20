<?php
	
	class ProductIconsManager
	{
		const DAILY_SECONDS = 24 * 60 * 60;
		/**
		 * @var structureManager
		 */
		protected $structureManager;
		/**
		 * @var parametersManager
		 */
		protected $parametersManager;
		/**
		 * @var Illuminate\Database\Capsule\Manager()
		 */
		protected $db;
		/**
		 * @var IconsManager $iconsManager
		 */
		protected $iconsManager;
		/**
		 * @var linksManager
		 */
		protected $linksManager;
		/**
		 * @var genericIconElement[]
		 */
		protected $icons = [];
		/**
		 * @var galleryImageElement[]
		 */
		protected $ownIcons = [];
		protected $iconProducts = [];
//    protected $iconElement = [];
		
		/**
		 * @param mixed $structureManager
		 */
		public function setStructureManager($structureManager)
		{
			$this->structureManager = $structureManager;
		}
		
		/**
		 * @param $parametersManager
		 */
		public function setParametersManager($parametersManager)
		{
			$this->parametersManager = $parametersManager;
		}
		
		/**
		 * @param Illuminate\Database\Capsule\Manager() $db
		 */
		public function setDb($db)
		{
			$this->db = $db;
		}
		
		/**
		 * @param mixed $linksManager
		 */
		public function setLinksManager($linksManager)
		{
			$this->linksManager = $linksManager;
		}
		
		/**
		 * @param mixed $iconsManager
		 */
		
		public function setIconsManager($iconsManager)
		{
			$this->iconsManager = $iconsManager;
		}
		
		/**
		 * @param productElement $product
		 * @return genericIconElement[]
		 */
		public function getProductIcons($product)
		{
			if (!isset($this->icons[$product->id])) {
				$this->icons[$product->id] = [];
				/**
				 * @var categoriesElement $categories
				 *
				 */
//				$categories = $product->getDeepParentCategories();
				$elementIconsIndex = [];
//				foreach ($categories as $category) {
//					$elementIconsIndex += $this->linksManager->getConnectedIdIndex($category->id, 'genericIconCategory', 'child');
//				}
//
//				//directly connected global icons
//				$elementIconsIndex += $this->linksManager->getConnectedIdIndex($product->id, 'genericIconProduct', 'child');
				
				//check all other icons for their logic
				/**
				 * @var array $allIcons
				 */
				$selectedElementsInIconIs = 0;
				if ($allIcons = $this->iconsManager->getAllIcons()) {
					/**
					 * @var array $productCategoriesIds
					 */
					// get Categories ID List of current product
					$productCategoriesIds = $product->getDeepParentCategoriesIdList();
					$productBrandId = $product->brandId;
					
					$now = time();

					foreach ($allIcons as $iconElement) {
						/**
						 * @var object $iconElement
						 */
						$elementIconsIndex[$iconElement->id] = false;
//						$selectedCategoriesInIconIs = false;
//						$selectedProductsInIconIs = false;
//						$selectedBrandsInIconIs = false;
						$selectedElementsInIconIs = 0;
						
						// check is selected categories exists
						// get selected Categories ID List of current icon
						if(!empty($productConnectedCategoriesIds = $iconElement->getConnectedCategoriesIds())){
							if (!empty(array_intersect($productCategoriesIds, $productConnectedCategoriesIds))){
								$selectedElementsInIconIs++;
							}
						}
						
						// check is selected brands exists
						// get selected Brands ID List of current icon
						if(!empty($productConnectedBrandsIds = $iconElement->getConnectedBrandsIds())){
							// get Brand ID List of current product
							
							if (!empty(in_array($productBrandId, $productConnectedBrandsIds))){
								$selectedElementsInIconIs++;
							}
						}
						
						// check is selected products exists
						// get selected Products ID List of current icon
						if(!empty($productConnectedProductsIds = $iconElement->getConnectedProductsIds())){
							
							if (in_array($product->id, $productConnectedProductsIds)){
								$selectedElementsInIconIs++;
//								$elementIconsIndex[$iconElement->id] = true;
							}
						}
						if($selectedElementsInIconIs>0) {
							//   var_dump(array_keys($selectedElementsInIconIs));
							//   continue;
							/**
							 * @var object $iconElement
							 */
							
							// by date
							if (!empty($iconRoleValue = $iconElement->productIconRoleTypes[$iconElement->iconRole]) and
								$iconRoleValue == 'role_date') {
								$startDate = $iconElement->getValue('startDate');
								if ($endDate = $iconElement->getValue('endDate')) {
									$endDate += self::DAILY_SECONDS;
								}
								$dateCreated = $product->getValue('dateCreated');
								if ($startDate && $endDate) {
									if ($startDate <= $dateCreated && $endDate >= $dateCreated) {
										$elementIconsIndex[$iconElement->id] = true;
									}
								} elseif ($startDate && $startDate <= $dateCreated) {
									$elementIconsIndex[$iconElement->id] = true;
								} elseif ($endDate && $endDate >= $dateCreated) {
									$elementIconsIndex[$iconElement->id] = true;
								}
								
								if ($dateCreated + $iconElement->days * self::DAILY_SECONDS >= $now) {
									$elementIconsIndex[$iconElement->id] = true;
								}
							}
							
							// by simple
							if (!empty($iconRoleValue = $iconElement->productIconRoleTypes[$iconElement->iconRole]) and
								$iconRoleValue == 'role_simple') {
								$elementIconsIndex[$iconElement->id] = true;
							}
							
							// by availability
							if (!empty($iconRoleValue = $iconElement->productIconRoleTypes[$iconElement->iconRole]) and
								$iconRoleValue == 'role_availability' and
								!empty($iconProductAvail = $iconElement->iconProductAvail)) {
								if (in_array($product->availability, $iconProductAvail)) {
									$elementIconsIndex[$iconElement->id] = true;
								}
							}
							
							// by general_discount
							/**
							 * @var genericIconElement $iconProductAvail
							 */
							if (!empty($iconRoleValue = $iconElement->productIconRoleTypes[$iconElement->iconRole]) and
								$iconRoleValue == 'role_general_discount' and
								$product->getDiscountAmount(false) > 0) {
								$elementIconsIndex[$iconElement->id] = true;
							}
							
							
							// by parameters (selection)
							// get parameters List (productSelection only) of current product
							if (!empty($iconRoleValue = $iconElement->productIconRoleTypes[$iconElement->iconRole]) and
								$iconRoleValue == 'role_by_parameter' and
								// get parameters id List of current icon
								!empty($iconConnectedParametersIds = $iconElement->getConnectedParametersIds())) {
								
/*								$productSelectionOptionsValIds = [];
								if ($productSelectionElements = $this->structureManager->getElementsByType('productSelection')) {
									foreach ($productSelectionElements as &$productSelectionElement) {
										$selectionId = $productSelectionElement->id;
										$productSelectionOptions = $this->structureManager->getElementsChildren($selectionId);
									}
									$productSelectionOptionsValIds1 = array_column($productSelectionOptions,'id');
									foreach ($productSelectionOptionsValIds1 as $productSelectionOptionsValId){
										$values = $this->parametersManager->getProductParameterValues($product->id, $productSelectionOptionsValId);
										var_dump($values);
									}
}
*/
								
								$productSelectionOptions = [];
								$productSelectionOptionsIds = [];
								if (!empty($parametersInfoList = $product->getParametersInfoList())) {
	                                foreach ($parametersInfoList as $parameterInfoKey=>$parameterInfoValue) {
	                                if ($parameterInfoValue['structureType'] == 'productSelection') {
											$productSelectionOptions = array_merge($productSelectionOptions, $parameterInfoValue['productOptions']);
											/*
											   'title' =>string
											   'id' =>int
											   'originalName' =>string
											   'image' =>string
											   'value' =>string
											*/
										}
									}
									// get id List of this parameters List
									$productSelectionOptionsIds = array_column($productSelectionOptions, 'id');
								}
								
								// get intersected parameters id List
								$productCurrentSelectionConnectedParametersIds =
									array_intersect($productSelectionOptionsIds, $iconConnectedParametersIds);
//								var_dump('pars array_intersect: ', $productSelectionOptionsTotalIds);
								
								if (!empty($productCurrentSelectionConnectedParametersIds)){
									$elementIconsIndex[$iconElement->id] = true;
								}
							} // by parameters
						} // !empty($selectedElementsInIconIs
						
					}
					
					
					foreach ($elementIconsIndex as $iconId => $value) {
						if ($value){
							$this->icons[$product->id][] = $this->structureManager->getElementById($iconId);
							
						}
					}
				}
				
				//add product's own icons
				if ($ownIcons = $this->getOwnIcons($product->id, $product->structureType)) {
					$this->icons[$product->id] = array_merge($this->icons[$product->id], $ownIcons);
				}
				
				//add parent categories' own and global unique icons
				foreach ($categories as $category) {
					if ($categoryIcons = $this->getCategoryIcons($category)) {
						foreach ($categoryIcons as $categoryIcon) {
							if (!isset($elementIconsIndex[$categoryIcon->id])) {
								$this->icons[$product->id][] = $categoryIcon;
								$elementIconsIndex[$categoryIcon->id] = true;
							}
						}
					}
					//	var_dump($category->id);
				}
			}
			return $this->icons[$product->id];
		}
		
		/**
		 * @param categoryElement $category
		 * @return mixed
		 */
		public function getCategoryIcons($category)
		{
			if (!isset($this->icons[$category->id])) {
				//category's own icons
				$this->icons[$category->id] = $this->getOwnIcons($category->id, $category->structureType);
				
				//parent categories' own icons
				$parentCategory = $category;
				while ($parentCategory = $parentCategory->getParentCategory()) {
					if ($parentCategoryIcons = $this->getOwnIcons($parentCategory->id, $parentCategory->structureType)) {
						$this->icons[$category->id] = array_merge($this->icons[$category->id], $parentCategoryIcons);
					}
				}
			}
			return $this->icons[$category->id];
		}
		
		public function getOwnIcons($id, $elementType)
		{
			if (!isset($this->ownIcons[$id])) {
				$this->ownIcons[$id] = $this->structureManager->getElementsChildren($id, null, $elementType . 'Icon');
			}
			return $this->ownIcons[$id];
		}
		
		public function getIconProductIds($iconId)
		{
			if (!isset($this->iconProducts[$iconId])) {
				$this->iconProducts[$iconId] = [];
			}
			if ($iconElement = $this->getIconElement($iconId)) {
				$this->iconProducts[$iconId] = $iconElement->getConnectedProductsIds();
				if ($ownCategoryIds = $iconElement->getConnectedCategoriesIds()) {
					foreach ($ownCategoryIds as $categoryId) {
						/**
						 * @var categoryElement $categoryElement
						 */
						if ($categoryElement = $this->structureManager->getElementById($categoryId)) {
							if ($categoryProductIds = $categoryElement->getConnectedProductsIds()) {
								$this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], $categoryProductIds);
							}
						}
					}
				}
				if ($iconElement->days) {
					if ($records = $this->db->table('structure_elements')
						->select('id')->distinct()
						->where('structureType', '=', 'product')
						->where('dateCreated', '>=', time() - self::DAILY_SECONDS * $iconElement->days)
						->get()
					) {
						$this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], array_column($records, 'id'));
					};
				}
				if ($iconElement->startDate || $iconElement->endDate) {
					$query = $this->db->table('structure_elements')
						->select('id')
						->distinct()
						->where('structureType', '=', 'product');
					if ($iconElement->startDate) {
						$query->where('dateCreated', '>=', $iconElement->getValue('startDate'));
					}
					if ($iconElement->endDate) {
						$query->where('dateCreated', '<=', $iconElement->getValue('endDate') + self::DAILY_SECONDS);
					}
					if ($records = $query->get()) {
						$this->iconProducts[$iconId] = array_merge($this->iconProducts[$iconId], array_column($records, 'id'));
					};
				}
			}
			return $this->iconProducts[$iconId];
		}
		
		public function getIconElement($iconId)
		{
			if ($allIcons = $this->iconsManager->getAllIcons()) {
				foreach ($allIcons as $iconElement) {
					if ($iconElement->id == $iconId) {
						return $iconElement;
					}
				}
			}
			return false;
		}
	}