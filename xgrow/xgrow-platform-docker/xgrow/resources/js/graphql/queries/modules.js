/** USED FOR AXIOS */
export const GET_MODULES_BY_PARAMS_QUERY_AXIOS = `
query($course_id: String, $page: Float, $limit: Float) {
  modules: retrieveModulesByParams(
    filters: { course_id: $course_id }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name
      position
      status
      courses {
        id
        name
      }
      Content {
        id
        title
        subtitle
        duration
        is_published
        horizontal_image
        order_content
        module_id
      }
    }
  }
}`;

export const GET_MODULE_BY_ID_QUERY_AXIOS = `
query($id: String!) {
  module: retrieveModuleById(id: $id) {
    id
    name
    position
    status
    course_id
    Content {
      title
      subtitle
      duration
      is_published
      horizontal_image
    }
  }
}`;

export const GET_MODULES_DELIVERY_QUERY_AXIOS = `
query($course_id: String, $page: Float, $limit: Float) {
  modules: retrieveModulesByParams(
    filters: { course_id: $course_id }
    paginate: { page: $page, limit: $limit }
  ) {
    total
    data {
      id
      name
      position
      courses {
        id
        name
      }
      Content {
        id
        title
        subtitle
        duration
        is_published
        horizontal_image
        order_content
        form_delivery
        delivery_model
        delivery_option
        frequency
        started_at
        delivered_at
      }
    }
  }
}`;

export const GET_ALL_MODULES_DELIVERY_QUERY_AXIOS = `
query {
  modules: retrieveModulesByParams(filters: {}, paginate: {}) {
    total
    data {
      id
      name
      updated_at
    }
  }
}`;
