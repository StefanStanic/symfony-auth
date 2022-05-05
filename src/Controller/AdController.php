<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
	{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
		{
		$this->em = $em;
		}

	/**
	 * @Route("/ads/create", name="ad_create")
	 * @IsGranted("ROLE_USER")
	 * @throws Exception
	 */
	public function create(): Response
		{
		$options = [Ad::TRANSACTION_RENT, Ad::TRANSACTION_SALE];


		$ad = new Ad();
		$ad
			->setTitle('Some cool title')
			->setDescription('Cake pudding chocolate donut pie muffin. Macaroon sesame snaps chocolate cake sesame snaps pastry sweet.')
			->setPrice(random_int(9999, 99999))
			->setImage('https://api.lorem.space/image/house?w=250&h=250&hash=' . random_int(100000, 999999))
			->setTransaction($options[random_int(0, 1)])
			->setOwner($this->em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]));

		$this->em->persist($ad);
		$this->em->flush();


		return $this->render('ad/create.html.twig', [
			'ad' => $ad,
		]);
		}

	/**
	 * @Route("/ads/edit/{id}", name="ad_edit")
	 * @IsGranted("ROLE_USER")
	 * @throws Exception
	 */
	public function edit(Ad $ad): Response
		{
		$ad->setPrice(random_int(9999, 99999));

		$this->em->flush();

		return $this->render('ad/show.html.twig', [
			'ad' => $ad,
		]);
		}

	/**
	 * @Route("/ads/delete/{id}", name="ad_delete")
	 * @IsGranted("ROLE_USER")
	 */
	public function delete(Ad $ad): RedirectResponse
		{
		$this->em->remove($ad);

		return $this->redirectToRoute('ad_collection');
		}

	/**
	 * @Route("/ads/{id}", name="ad_show")
	 */
	public function show(Ad $ad): Response
		{
		return $this->render('ad/show.html.twig', [
			'ad' => $ad,
		]);
		}

	/**
	 * @Route("/ads", name="ad_collection")
	 */
	public function collection(Request $request): Response
		{
		$adRepository = $this->em->getRepository(Ad::class);
		$transaction  = $request->query->get('transaction');

		$ads = $transaction
			? $adRepository->findByTransaction($transaction)
			: $adRepository->findAll();

		return $this->render('ad/collection.html.twig', [
			'ads' => $ads,
		]);
		}
	}
