/** USED FOR AXIOS */
export const ALL_COURSES_QUERY_AXIOS = `
query($name: String, $page: Float, $limit: Float) {
    courses: retrieveCoursesByParams(
        data: { name: $name }
        paginate: { page: $page, limit: $limit }
    ) {
        total
        data {
            id
            name
            author_id
            authorOld: author_id
            is_experience
            Modules {
                name
                Content {
                    title
                }
            }
            author: Authors {
                name_author
                author_email
            }
            vertical_image
            horizontal_image
            active
            updated_at
        }
    }
}`;

/** Get Course by params */
export const GET_COURSE_BY_PARAMS_QUERY_AXIOS = `
query(
  $id: String
  $name: String
  $active: Boolean
  $page: Float
  $limit: Float
) {
  courses: retrieveCoursesByParams(
    data: { id: $id, name: $name, active: $active }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name
      description
      active
      platform_id
      author_id
      Authors {
        name_author
        author_email
        author_photo_url
      }
      horizontal_image
      vertical_image
      has_offer_link
      offer_link
      created_at
      updated_at
    }
  }
}`;

export const GET_COURSE_BY_ID_QUERY_AXIOS = `
query($id: String!) {
  course: retrieveCourseById(id: $id) {
      id
      name
      description
      active
      platform_id
      author_id
      Authors {
        name_author
        author_email
      }
      horizontal_image
      vertical_image
      has_offer_link
      offer_link
      created_at
      updated_at
  }
}`;

export const ALL_COURSES_FOR_DELIVERY_QUERY_AXIOS = `
query($active: Boolean, $page: Float, $limit: Float) {
  courses: retrieveCoursesByParams(
    data: { active: $active }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name
    }
  }
}`;
