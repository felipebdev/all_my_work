export const GET_ALL_COMMENTS_QUERY = `
query ($page: Float, $limit: Float, $approved: Boolean) {
	comments: getAllComments(
		params: { approved: $approved }
		pagination: { page: $page, limit: $limit }
	) {
		total
		total_pages
		current_page
		data {
			id
			text
			approved
			user_id
			approved_by
			content_id
			Content {
				id
				title
				contentType
                horizontal_image
			}
			created_at
			total_answers
			reactions {
				like
				dislike
				love
				haha
				wow
			}
		}
	}
}`;


export const GET_ANSWERS_QUERY = `
query ($comment_id: String!) {
	answers: getAnswers(
		params: { comment_id: $comment_id }
		pagination: { page: 1, limit: 10 }
	) {
		total
		total_pages
		current_page
		data {
			id
			text
			approved
			user_id
			created_at
            is_answer_by_producer
			reactions {
				like
				dislike
				love
				haha
				wow
			}
		}
	}
}`;