/** USED FOR AXIOS */
export const GET_ALL_AUTHORS_QUERY_AXIOS = `
query(
  $name_author: String
  $author_email: String
  $page: Float
  $limit: Float
) {
  authors: retrieveAuthorByEmailByName(
    data: { name_author: $name_author, author_email: $author_email }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name_author
      author_desc
      author_email
      author_photo_url
      status
    }
  }
}`;

export const ALL_AUTHORS_QUERY_AXIOS = `
query(
  $name_author: String
  $author_email: String
  $page: Float
  $limit: Float
) {
  authors: retrieveAuthorByEmailByName(
    data: { name_author: $name_author, author_email: $author_email }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name_author
      author_desc
      author_email
      author_photo_url
      status
    }
  }
}`;

export const GET_AUTHOR_BY_PARAMS_QUERY_AXIOS = `
query(
  $id: String
  $name_author: String
  $author_email: String
  $page: Float
  $limit: Float
) {
  author: retrieveAuthorsByParams(
    data: { id: $id, name_author: $name_author, author_email: $author_email }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
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
  }
}`;
