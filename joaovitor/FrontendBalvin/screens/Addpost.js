import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import { firebase } from './Config/Firebase';
import * as ImagePicker from 'expo-image-picker';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';


const AddPost = ({ navigation }) => {

    const [postdescription, setpostdescription] = useState('')

    const [loading1, setLoading1] = useState(false)
    const [loading2, setLoading2] = useState(false)
    const [post, setPost] = useState('')

    const pickImage = async () => {
        setLoading1(true)
        let result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [1, 1],
            quality: 1,
        })
        // console.log(result)


        if (!result.cancelled) {
            const source = { uri: result.uri };


            const response = await fetch(result.uri);
            const blob = await response.blob();
            const filename = result.uri.substring(result.uri);

            const ref = firebase.storage().ref().child(filename);
            const snapshot = await ref.put(blob);
            const url = await snapshot.ref.getDownloadURL();

            setLoading1(false)
            setPost(url)
            // console.log(url)
        }
        else {
            setLoading1(false)
            setPost(null)
            
        }
    }

    const handleUpload = () => {

        if (post != null) {
            AsyncStorage.getItem('user')
                .then(data => {
                    setLoading2(true)

                    fetch('http://192.168.0.54:3000/addpost', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: JSON.parse(data).user.email,
                            post: post,
                            postdescription: postdescription
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.message == 'Postagem adicionada com sucesso') {
                                setLoading2(false)
                                navigation.navigate('Perfil')
                            }
                            else {
                                alert('Algo deu errado, please try again')
                                setLoading2(false)
                            }
                        })
                })
        }
        else {
            alert('Please select an image')
        }
    }

    return (
        <View style={{  width: '100%',
        height: '100%',
        backgroundColor: '#fafafa',}}>

            <View style={{width: '100%', flexDirection: 'row', height: 50}}>
            <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}} style={{width: '100%', flexDirection: 'row', height: 50}}>
            <TouchableOpacity onPress={() => navigation.navigate('Homescreen')} style={{justifyContent: 'center'}}>
            <MaterialIcons name="arrow-back-ios" size={24} color="white" style={{marginStart: 10}} />
            </TouchableOpacity>

            <Text
            style={{fontSize: 15, color: 'white', fontWeight: 'bold', marginStart: 100, justifyContent: 'center',
            alignSelf: 'center'}}>Nova Publicação</Text>


{
                loading2 ? <ActivityIndicator
                    size="large"
                    color="white" style={{marginStart: 75}}
                /> :
                    <Text style={{marginStart: 70, fontSize: 14, alignSelf: 'center', color: 'white', fontWeight: 'bold'}}
                        onPress={() => handleUpload()}
                    >
                        Concluir
                    </Text>
            }
            </LinearGradient>
            </View>
          
            <View style={{justifyContent: 'center', marginTop: 300, height: 200, alignItems: 'center'}}>

            {
                loading1 ? <ActivityIndicator
                    size="large"
                    color="white"
                /> :
                    <>

                        {
                            post ?
                                <TouchableOpacity
                                    onPress={() => pickImage()}
                                >
                                    <Image source={{ uri: post }} style={{
                                        width: 350, height: 350,
                                        marginTop: -390,
                                    }} />
                                </TouchableOpacity>
                                :
                                <TouchableOpacity   onPress={() => {
                                    pickImage()
                                }}>
                                <Image source={require('../images/camera.png')} style={{width: 150, height: 150, marginTop: -200}}/>
                                </TouchableOpacity>
                        }
                    </>
            }

            </View>


           
        </View>
    )
}






export default AddPost

const styles = StyleSheet.create({
    addpost: {
        fontSize: 18,
        fontWeight: 'bold',
        color: '#E0230D',
        backgroundColor: '#FAFAFA',
        borderColor: 'white',
        borderWidth: 1,
        borderRadius: 10,
        paddingVertical: 20,
        width: 350,
        textAlign: 'center',
        marginVertical: 20,
    }
})