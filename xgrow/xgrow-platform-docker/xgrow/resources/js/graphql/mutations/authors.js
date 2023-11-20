/** USED FOR AXIOS */
export const SAVE_FAST_AUTHOR_MUTATION_AXIOS = `
mutation(
    $name_author: String!
    $author_email: String
    $author_photo_url: String = "https://las.xgrow.com/background-default.png"
    $status: Boolean!
) {
    createAuthor(
        data: {
            name_author: $name_author
            author_email: $author_email
            author_photo_url: $author_photo_url
            status: $status
        }
    ) {
        id
        name_author
        author_desc
        author_email
        status
    }
}`;

export const SAVE_AUTHOR_MUTATION_AXIOS = `
mutation(
    $name_author: String!
    $author_desc: String
    $author_email: String
    $author_insta: String
    $author_linkedin: String
    $author_youtube: String
    $author_photo_url: String = "https://las.xgrow.com/background-default.png"
    $status: Boolean!
) {
    createAuthor(
        data: {
            name_author: $name_author
            author_desc: $author_desc
            author_email: $author_email
            author_insta: $author_insta
            author_linkedin: $author_linkedin
            author_youtube: $author_youtube
            author_photo_url: $author_photo_url
            status: $status
        }
    ) {
        id
        name_author
        author_desc
        author_email
        status
    }
}`;

export const UPDATE_AUTHOR_MUTATION_AXIOS = `
mutation(
    $id: String!
    $name_author: String
    $author_desc: String
    $author_email: String
    $author_insta: String
    $author_linkedin: String
    $author_youtube: String
    $author_photo_url: String = "https://las.xgrow.com/background-default.png"
    $status: Boolean
) {
    author: updateAuthorById(
        id: $id
        data: {
            name_author: $name_author
            author_desc: $author_desc
            author_email: $author_email
            author_insta: $author_insta
            author_linkedin: $author_linkedin
            author_youtube: $author_youtube
            author_photo_url: $author_photo_url
            status: $status
        }
    ) {
        id
        name_author
        author_desc
        author_email
        author_insta
        author_linkedin
        author_youtube
        author_photo_url
        status
    }
}`;

export const DELETE_AUTHOR_MUTATION_AXIOS = `
mutation($id: String!) {
  author: deleteAuthorById(id: $id) {
    id
    name_author
  }
}`;
