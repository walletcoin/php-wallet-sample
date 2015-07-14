<?php

namespace BlockCypher\AppWallet\Infrastructure\AppWalletBundle\Controller\Transaction;

use BlockCypher\AppWallet\Infrastructure\AppWalletBundle\Controller\AppWalletController;
use BlockCypher\AppWallet\Presentation\Facade\WalletServiceFacade;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class Index
 * @package BlockCypher\AppWallet\Infrastructure\AppWalletBundle\Controller\Transaction
 */
class Index extends AppWalletController
{
    /**
     * @var WalletServiceFacade
     */
    private $walletServiceFacade;

    /**
     * @param EngineInterface $templating
     * @param TranslatorInterface $translator
     * @param Session $session
     * @param WalletServiceFacade $walletServiceFacade
     */
    public function __construct(
        EngineInterface $templating,
        TranslatorInterface $translator,
        Session $session,
        WalletServiceFacade $walletServiceFacade
    )
    {
        parent::__construct($templating, $translator, $session);
        $this->walletServiceFacade = $walletServiceFacade;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function __invoke(Request $request)
    {
        $walletId = $request->get('walletId');

        $walletDto = $this->walletServiceFacade->getWallet($walletId);
        $walletTransactionDto = $this->walletServiceFacade->listWalletTransactions($walletId);
        $transactions = $walletTransactionDto->getTransactionListItemDtos();

        $template = $this->getBaseTemplatePrefix() . ':Transaction:index.html';

        // DEBUG
        //var_dump($walletTransactionDto);
        //die();

        // TODO
        $currentPage = 1;
        $maxPages = 0; // get_max_pages(num_items=address_details['final_n_tx'], items_per_page=TXNS_PER_PAGE),

        $BLOCKCYPHER_PUBLIC_KEY = "c0afcccdde5081d6429de37d16166ead";

        return $this->templating->renderResponse(
            $template . '.' . $this->getEngine(),
            array(
                // TODO: move to base controller and merge arrays
                'is_home' => false,
                'user' => array('is_authenticated' => true),
                'messages' => array(),
                //
                'coin_symbol' => 'btc',
                'current_page' => $currentPage,
                'num_all_wallets' => count($transactions),
                'max_pages' => $maxPages,
                'wallet_id' => $walletId,
//                'total_sent_satoshis' => $walletTransactionDto->getTotalSent(),
//                'total_received_satoshis' => $walletTransactionDto->getTotalReceived(),
//                'total_balance_satoshis' => $walletTransactionDto->getFinalBalance(),
//                'unconfirmed_balance_satoshis' => $walletTransactionDto->getUnconfirmedBalance(),
                'num_all_txns' => $walletTransactionDto->getNTx(),
                'num_unconfirmed_txns' => $walletTransactionDto->getUnconfirmedNTx(),
                'wallet' => $walletDto,
                'all_transactions' => $transactions,
                'BLOCKCYPHER_PUBLIC_KEY' => $BLOCKCYPHER_PUBLIC_KEY
            )
        );
    }
}