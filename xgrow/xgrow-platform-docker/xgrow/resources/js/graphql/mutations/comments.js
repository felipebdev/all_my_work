export const UPDATE_COMMENT_APPROVE_MUTATION = `
mutation ($comment_id: String!, $approved: Boolean!) {
	updateCommentApprove(data: { comment_id: $comment_id, approved: $approved }) {
		id
		approved
	}
}`;

export const DELETE_COMMENT_MUTATION = `
mutation ($id: String!) {
	deleteComment(id: $id) {
		id
		Content {
			id
			title
		}
	}
}`;

/** ANSWERS */
export const CREATE_ANSWER_MUTATION = `
mutation ($comment_id: String!, $text: String!) {
	createAnswer(data: { comment_id: $comment_id, text: $text }) {
		id
	}
}`

export const DELETE_ANSWER_MUTATION = `
mutation ($answer_id: String!) {
	deleteAnswer(answer_id: $answer_id) {
		id
	}
}`