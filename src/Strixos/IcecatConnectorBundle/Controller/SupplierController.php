<?php
namespace Strixos\IcecatConnectorBundle\Controller;

use Strixos\IcecatConnectorBundle\Model\BaseExtractor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use APY\DataGridBundle\Grid\Source\Entity as GridEntity;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 *
 * @author    Romain Monceau @ Akeneo
 * @copyright Copyright (c) 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class SupplierController extends Controller
{
    /**
     * @Route("/supplier/load-from-icecat")
     * @Template()
     */
    public function loadFromIcecatAction()
    {
        try {

            $srvConnector = $this->container->get('akeneo.connector.icecat_service');
            $srvConnector->importSuppliers();

        } catch (\Exception $e) {
            return array('exception' => $e);
        }

        return $this->redirect($this->generateUrl('strixos_icecatconnector_supplier_list'));
    }

    /**
     * List Icecat suppliers in a grid
     * @Route("/supplier/list")
     * @Template()
     */
    public function listAction()
    {
        // creates simple grid based on entity (ORM)
        $source = new GridEntity('StrixosIcecatConnectorBundle:Supplier');
        $grid = $this->get('grid');
        $grid->setSource($source);
        // add an action column to load import of all products of a supplier
        $rowAction = new RowAction('Import products to PIM', 'strixos_icecatconnector_supplier_loadproducts');
        $rowAction->setRouteParameters(array('icecatId'));
        $grid->addRowAction($rowAction);
        // manage the grid redirection, exports response of the controller
        return $grid->getGridResponse('StrixosIcecatConnectorBundle:Supplier:grid.html.twig');
    }

    /**
     * List Icecat suppliers in a grid
     * @Route("/supplier/load-products/{icecatId}")
     * @Template()
     */
    public function loadProductsAction($icecatId)
    {
        try {
            $supplier = $this->getDoctrine()->getRepository('StrixosIcecatConnectorBundle:Supplier')
                    ->findOneByIcecatId($icecatId);

            $srvConnector = $this->container->get('akeneo.connector.icecat_service');
            $srvConnector->importProductsFromSupplier($supplier);

            //$products = $this->getDoctrine()->getRepository('StrixosIcecatConnectorBundle:Product')->find($id);

        } catch (Exception $e) {
            return array('exception' => $e);
        }

        $products = $this->getDoctrine()->getRepository('StrixosIcecatConnectorBundle:Product')
                ->findBySupplier($supplier);

        return array('supplier' => $supplier, 'products' => $products);
    }
}