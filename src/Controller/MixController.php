<?php

namespace App\Controller;

use App\Entity\VinylMix;
use App\Repository\VinylMixRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MixController extends AbstractController
{
    #[Route('/mix/new')]
    public function new(EntityManagerInterface $entityManager): Response
    {
        $mix = new VinylMix();
        $mix->setTitle('Testing out the title');
        $mix->setDescription('I have no idea what to write');
        $genres = ['pop', 'rock'];
        $mix->setGenre($genres[array_rand($genres)]);
        $mix->setTrackCount(rand(5, 20));
        $mix->setVotes(rand(-50, 50));

        //persist makes doctrine aware of the object
        //flush will then save it
        $entityManager->persist($mix);
        $entityManager->flush();

        return new Response(sprintf(
            'Mix %d is %d tracks of pure 80\'s heaven',
            $mix->getId(),
            $mix->getTrackCount()
        ));
    }

    #[Route('/mix/{id}', name: 'app_mix_show')]
    public function show(VinylMix $mix)
    {
        return $this->render('mix/show.html.twig', [
            'mix' => $mix,
        ]);
    }

    #[Route('//mix/{id}/vote', name: 'app_mix_vote', methods: ['POST'])]
    public function vote(VinylMix $mix, Request $request, EntityManagerInterface $entityManager): Response
    {

        $direction = $request->request->get('direction', 'up');
        if($direction === 'up'){
            $mix->setVotes($mix->getVotes() + 1);
        }else{
            $mix->setVotes($mix->getVotes() - 1);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_mix_show', [
           'id' => $mix->getId(),
        ]);
    }

}