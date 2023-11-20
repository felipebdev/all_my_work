import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator} from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import { firebase } from '../Config/Firebase'
import * as ImagePicker from 'expo-image-picker';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Addfotoperfil = ({ navigation }) => {

    const [image, setImage] = useState(null);

    const [loading, setLoading] = useState(false)

    const pickImage = async () => {
        let result = await ImagePicker.launchImageLibraryAsync({
            mediaTypes: ImagePicker.MediaTypeOptions.Images,
            allowsEditing: true,
            aspect: [1, 1],
            quality: 1,
        })
        // console.log(result)


        if (!result.cancelled) {
            const source = { uri: result.uri };
            setImage(source);

            const response = await fetch(result.uri);
            const blob = await response.blob();
            const filename = result.uri.substring(result.uri);

            const ref = firebase.storage().ref().child(filename);
            const snapshot = await ref.put(blob);
            const url = await snapshot.ref.getDownloadURL();

            // console.log(url)
            return url
        }
        else {
            return null
        }
    }

    const handleUpload = () => {
        // pickImage()
        AsyncStorage.getItem('user')
            .then(data => {
                setLoading(true)

                pickImage().then(url => {
                    fetch('http://192.168.0.54:3000/fotodeperfil', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: JSON.parse(data).user.email,
                            profilepic: url
                        })
                    })
                        .then(res => res.json()).then(
                            data => {
                                if (data.message === "Foto do perfil atualizada com sucesso") {
                                    setLoading(false)
                                    navigation.navigate('Perfil')
                                }
                                else if (data.error === "Credenciais inválidas") {
                                   
                                    setLoading(false)
                                    navigation.navigate('Login')
                                }
                                else {
                                    setLoading(false)
                                    alert("Tente novamente");
                                }
                            }
                        )
                        .catch(err => {
                            console.log(err)
                        })

                })
            })
    }
    return (
        <View >
            <View style={{width: '100%', flexDirection: 'row', height: 50, backgroundColor: '#ec230d', alignItems: 'center'}}>
            <TouchableOpacity onPress={() => navigation.navigate('Perfil')} style={{marginStart: 20}}>

                <MaterialIcons name="arrow-back-ios" size={24} color="white" />
               

            </TouchableOpacity>

            <Text style={{fontSize: 17, marginStart: 100, fontWeight: 'bold', color: 'white'}}>Foto de perfil</Text>
           
            </View>

            {
                loading ? <ActivityIndicator
                    size="large"
                    color="white"
                    style={{marginTop: 50}}
                /> :
                    <Text style={{alignSelf: 'center' ,fontSize: 25, color: '#ec230d', fontWeight: 'bold',
                width: 200, height: 100, backgroundColor: 'white', marginTop: 20, textAlign: 'center', borderRadius: 10,
            textAlignVertical: 'center', borderColor: '#ec230d', borderWidth: 0.7}}
                        onPress={() => handleUpload()}
                    >
                        Selecione
                    </Text>
            }

         
        </View>
    )
}





export default Addfotoperfil

const styles = StyleSheet.create({})