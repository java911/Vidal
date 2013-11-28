<?php
namespace Evrika\MainBundle\Command;

use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Evrika\MainBundle\Entity\Category;

class CalculateRatingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
			->setName('evrika:rating')
            ->setDescription('Recalculates user rating in the database according to the current rules')
            ->addArgument('user', InputArgument::OPTIONAL, 'ID of the user to recalculate rating')
			->addOption('email', null, InputOption::VALUE_NONE, 'If set, the argument is considered a user\'s e-mail rather than an ID')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Recalculates rating for all users in database (can take really long time!)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$em = $this->getContainer()->get('doctrine')->getEntityManager();
	
		if ($input->getOption('all')) {
			$users = $em
				->createQuery('SELECT u.id, u.rating AS old_rating FROM EvrikaMainBundle:User u')
				->getResult();
			
			$output->writeln('Total number of users found: ' . count($users));
			$output->writeln('Starting update.');
			
			foreach ($users as $user) {
				$output->write('Recalculating for user ID ' . $user['id'] . ': ');
				$this->recalculateRating($user, $output);
			}
			
		} else {
			if ($userIdOrName = $input->getArgument('user')) {
				if ($this->getOption('email')) {
					$user = $em->getRepository('EvrikaMainBundle:User')->findOneByUsername($userIdOrName);
				} else {
					$user = $em->find('EvrikaMainBundle:User', $userIdOrName);
				}
			} else {
				$output->writeln('Укажите ID пользователя или e-mail пользователя с ключом --email, или ключ --all чтобы пересчитать рейтинг всех пользователей');
				exit;
			}
			
			$this->recalculateRating($user, $output);
		}
		
	}
	
	private function recalculateRating($user, $output) 
	{
		$rating = 0;
		$em = $this->getContainer()->get('doctrine')->getEntityManager();
		
		$publications = $em->createQuery('SELECT p.id AS publication_id, c.id AS category_id, p.numberOfLikes FROM EvrikaMainBundle:Publication p JOIN p.category c WHERE p.author = ' . $user['id'])
			->getResult();
		
		if (!empty($publications)) {
			$output->write('Number of publications: ' . count($publications) . '. ');
			
			$countBookmarkedPublicationsDQL = 'SELECT COUNT(b.id) FROM EvrikaMainBundle:Bookmark b WHERE b.publication IN (';
			
			foreach ($publications as $publication) {
				switch ($publication['category_id']) {
					case Category::ARTICLE_CATEGORY_ID:
						$rating += 5;
						
						if ($publication['numberOfLikes'] >= 10) {
							$rating += 25;
						}
						
						break;
					case Category::VIDEO_CATEGORY_ID:
						$rating += 2;
						
						if ($publication['numberOfLikes'] >= 10) {
							$rating += 10;
						}
						
						break;
					case Category::COUNCIL_CATEGORY_ID:
						$rating += 3;
						
						if ($publication['numberOfLikes'] >= 10) {
							$rating += 15;
						}
						
						break;
				}
				
				$countBookmarkedPublicationsDQL .=  $publication['publication_id'] . ',';
			}
			
			$countBookmarkedPublicationsDQL = substr($countBookmarkedPublicationsDQL, 0, strlen($countBookmarkedPublicationsDQL) - 1) . ')';
			$numberOfBookmarkedPublications = $em->createQuery($countBookmarkedPublicationsDQL)->getSingleScalarResult();
			
			$output->write('Number of bookmarked publications: ' . $numberOfBookmarkedPublications . '. ');
			
			$rating += $numberOfBookmarkedPublications * 5;
		}
		
		$comments = $em->createQuery('SELECT c.id, c.numberOfLikes FROM EvrikaMainBundle:Comment c WHERE c.author = ' . $user['id'])
			->getResult();
		
		$numberOfComments = count($comments);
		
		if (!empty($comments)) {
			$output->write('Number of comments: ' . $numberOfComments . '. ');
			
			$counter = 0;
			
			foreach ($comments as $comment) {
				$counter ++;
				
				if ($counter == 5) {
					$counter = 0;
					$rating += 1;
				}
				
				if ($comment['numberOfLikes'] >= 5) {
					$rating += 1;
				}
			}
		}
		
		$output->write('Total rating:' . $rating);
		if ($rating == $user['old_rating']) {
			$output->writeln(' is the same, skipping.');
		} else {
			$output->writeln(' differs (current is ' . $user['old_rating'] . '), updating.');
			
			$em->createQuery('UPDATE EvrikaMainBundle:User u SET u.rating = ' . $rating . ', u.numberOfComments = ' . $numberOfComments . ' WHERE u.id = ' . $user['id'])
			->execute();
		}
	}
}