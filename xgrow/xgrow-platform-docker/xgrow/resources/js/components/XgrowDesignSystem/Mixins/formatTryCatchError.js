const formatTryCatchError = function(error) {

  if (error.response.data.errors) {
    const errorList = Object.values(error.response.data.errors);
    const partialMessage = errorList.map((item) => item.join("\n"));
    const finalMessage = partialMessage.join("\n");

    return finalMessage;
  }

  return error.response.data.message;
}

export default formatTryCatchError
