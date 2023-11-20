/** USED FOR AXIOS */
export const UPDATE_COURSE_AUTHOR_MUTATION_AXIOS = `
mutation($id: String!, $author_id: String) {
    course: updateCourseById(id: $id, data: { author_id: $author_id }) {
      id
      name
    }
}`;

export const UPDATE_COURSE_STATUS_MUTATION_AXIOS = `
mutation($id: String!, $active: Boolean!) {
    course: updateCourseById(id: $id, data: { active: $active }) {
        id
        name
        active
    }
}`;

export const DELETE_COURSE_MUTATION_AXIOS = `
mutation($id: String!) {
    course: deleteCourseById(id: $id) {
        id
        name
    }
}`;

export const UPDATE_COURSE_MUTATION_AXIOS = `
mutation(
  $id: String!
  $name: String!
  $description: String
  $active: Boolean
  $author_id: String
  $horizontal_image: String = "https://las.xgrow.com/background-default.png"
  $vertical_image: String = "https://las.xgrow.com/background-default.png"
  $has_offer_link: Boolean
  $offer_link: String
) {
  course: updateCourseById(
    id: $id
    data: {
      name: $name
      description: $description
      active: $active
      author_id: $author_id
      horizontal_image: $horizontal_image
      vertical_image: $vertical_image
      has_offer_link: $has_offer_link
      offer_link: $offer_link
    }
  ) {
    id
    name
    active
  }
}`;

export const SAVE_COURSE_MUTATION_AXIOS = `
mutation(
  $name: String!
  $description: String
  $active: Boolean
  $author_id: String!
  $horizontal_image: String = "https://las.xgrow.com/background-default.png"
  $vertical_image: String = "https://las.xgrow.com/background-default.png"
  $has_offer_link: Boolean
  $offer_link: String
) {
  createCourse(
    data: {
      name: $name
      description: $description
      active: $active
      author_id: $author_id
      horizontal_image: $horizontal_image
      vertical_image: $vertical_image
      has_offer_link: $has_offer_link
      offer_link: $offer_link
    }
  ) {
    id
    name
    active
    horizontal_image
  }
}`;
