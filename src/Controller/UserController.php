<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
	private UserRepository $repository;

	public function __construct(UserRepository $repository)
		{

		$this->repository = $repository;
		}

	/**
	 * @Route("/profile/ads", name="user_my_ads")
	 * @IsGranted("ROLE_USER")
	 */
	public function myAds(): Response
		{
		$user = $this->repository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

		return $this->render('user/my-ads.html.twig', [
			'ads' => $user->getAds(),
		]);
		}

	/**
	 * @Route("/profile", name="user_profile")
	 * @IsGranted("ROLE_USER")
	 */
	public function profile(): Response
		{
		return $this->render('user/index.html.twig');
		}
}
