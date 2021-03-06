<?php
    class modelProfile extends componentModel {

    	private function getFormattedDate($result) {
			$i = 0;
			foreach ($result as $item) {
				$date = date("d-m-y g:i A", strtotime($item['add_date']));
				$result[$i]['add_date'] = $date;
				$i++;
			}
			return $result;
		}

        public function getFivePostsProfile($elements) {
			$user = $_SESSION['user_logged'];
			$limit = 5 + (int)$elements;

			$query = "SELECT * FROM posts WHERE user = '$user' ORDER BY add_date DESC LIMIT $limit";
			$sth = $this->prepare($query);
			$sth->execute();
			$result = $sth->fetchAll(self::FETCH_ASSOC);

			$full = false;
			if ($result)
				$full = true;

			while ($elements--)
				array_shift($result);

			$i = 0;
			if (!empty($_SESSION['user_id'])) {
				$user = $_SESSION['user_id'];
				foreach ($result as $item) {
					$id = $item['id'];

					$sth = $this->query("SELECT list FROM likes WHERE post = '$id'");
					$list = $sth->fetchAll(self::FETCH_ASSOC);
					if (!empty($list)) {
						$list = explode(',', $list[0]['list']);
						$liked = '0';
						foreach ($list as $elem)
							if (preg_match("/$user/", $elem))
								$liked = '1';
					}
					else
						$liked = '0';
					$result[$i]['liked'] = $liked;
					$i++;
				}
			}

			if (!$full && empty($result))
				return ['empty' => true];

			if (!empty($result))
				$result = $this->getFormattedDate($result);

			return $result;
        }

        public function getNextPostProfile($postid) {
			$user = $_SESSION['user_logged'];

			$sth = $this->query("SELECT * FROM posts WHERE user = '$user' AND id < '$postid' ORDER BY add_date DESC LIMIT 1");
			$result = $sth->fetchAll(self::FETCH_ASSOC);
			if (!empty($result))
				$result[0]['add_date'] = date("m-d-y g:i A", strtotime($result[0]['add_date']));
			return $result;
		}

        public function deletePost($id) {
        	//delete photo from server
			$sth = $this->query("SELECT path FROM posts WHERE id = '$id'");
			$result = $sth->fetchAll(self::FETCH_ASSOC);
			unlink($result[0]['path']);

			//delete all data connected to this post
			$this->query("DELETE FROM posts WHERE id = '$id'");
			$this->query("DELETE FROM comments WHERE post = '$id'");
			$this->query("DELETE FROM likes WHERE post = '$id'");
		}

		public function getComments($id) {
			$sth = $this->query("SELECT author_avatar, author_login, 
							add_date, comment FROM comments WHERE post = '$id'");
			$result = $sth->fetchAll(self::FETCH_ASSOC);

			if (!empty($result))
				$result = $this->getFormattedDate($result);

			return $result;
		}

		public function addComment($post, $comment) {
			$author_login = $_SESSION['user_logged'];
			if ($_SESSION['avatar'] === false)
				$author_avatar = 'views/pictures/avatars/default.png';
			else
				$author_avatar = $_SESSION['avatar'];
			date_default_timezone_set('Europe/Kiev');
			$date = date("Y-m-d H:i:s");

			//get post owner
			$sth = $this->query("SELECT user FROM posts WHERE id = '$post'");
			$owner = $sth->fetchAll(self::FETCH_ASSOC);
			$owner = $owner[0]['user'];

			//add comment
			$sth = $this->prepare("INSERT INTO comments(post, owner, author_login, author_avatar, add_date, comment)
                 VALUES ('$post', '$owner', '$author_login', '$author_avatar','$date', :comment)");
			$sth->execute([':comment' => $comment]);

			//iterate comments counter and get value after
			$this->query("UPDATE posts SET comments = comments+1 WHERE id = '$post'");
			$sth = $this->query("SELECT comments FROM posts WHERE id = '$post'");
			$counter = $sth->fetchAll(self::FETCH_ASSOC);
			$counter = $counter[0]['comments'];

			//grab current comment
			$sth = $this->prepare("SELECT author_avatar, author_login, 
							add_date, comment FROM comments WHERE comment = :comment AND add_date = '$date'");
			$sth->execute([':comment' => $comment]);
			$result = $sth->fetchAll(self::FETCH_ASSOC);

			if (!empty($result))
				$result = $this->getFormattedDate($result);

			$result[0]['counter'] = $counter;
			return $result;
		}

		public function addLike($post) {
    		$user = $_SESSION['user_id'];

			$sth = $this->query("SELECT * FROM likes WHERE post = '$post'");
			$list = $sth->fetchAll(self::FETCH_ASSOC);
			$user = $user.',';
			$list = $list[0]['list'].$user;
			$this->query("UPDATE likes SET list = '$list' WHERE post = '$post'");
			$this->query("UPDATE posts SET likes = likes+1 WHERE id = '$post'");
		}

		public function prepareNotification($post) {
    		//if comment author is posts owner do not send letter
			$author = $author_id = $_SESSION['user_logged'];
			$sth = $this->query("SELECT owner, author_login FROM comments WHERE post = '$post' AND author_login = '$author' 
												ORDER BY add_date DESC LIMIT 1");
			$data = $sth->fetchAll(self::FETCH_ASSOC);
			if ($data[0]['owner'] === $data[0]['author_login'])
				return;

			$user = $data[0]['owner'];
			$sth = $this->query("SELECT email, notification FROM users WHERE login = '$user'");
			$data = $sth->fetchAll(self::FETCH_ASSOC);
			$email = $data[0]['email'];
			$status = $data[0]['notification'];

			//if user turned off notifications do not send letter
			if ($status === '0')
				return;
			$mail = new componentMail();
			$mail->sendNotification($user, $email);
		}
    }
